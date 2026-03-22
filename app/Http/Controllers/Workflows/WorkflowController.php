<?php

namespace App\Http\Controllers\Workflows;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\ReimbursementPolicy;
use App\Models\User;
use App\Models\WorkflowApproval;
use App\Models\WorkflowRequest;
use App\Models\WorkflowTemplate;
use App\Notifications\WorkflowApproved;
use App\Notifications\WorkflowFulfilled;
use App\Notifications\WorkflowRejected;
use App\Notifications\WorkflowSubmitted;
use App\Support\ActivityLogger;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WorkflowController extends Controller
{
    public function index(): View
    {
        return view('hrms.workflows.index', [
            'workflowTypes' => WorkflowRequest::types(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenantId = TenantContext::id();

        $query = WorkflowRequest::query()
            ->with([
                'requester:id,name,email',
                'employee:id,full_name',
                'template:id,name,type',
                'approvals.approver:id,name',
            ])
            ->latest('submitted_at');

        if (! $user->hasAnyRole(['admin', 'hr_manager'])) {
            $query->where(function ($builder) use ($user) {
                $builder->where('requester_user_id', $user->id)
                    ->orWhereHas('approvals', fn ($approvalQuery) => $approvalQuery->where('approver_user_id', $user->id));
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('scope')) {
            if ($request->scope === 'mine') {
                $query->where('requester_user_id', $user->id);
            }

            if ($request->scope === 'approvals') {
                $query->whereHas('approvals', fn ($approvalQuery) => $approvalQuery
                    ->where('approver_user_id', $user->id)
                    ->where('decision', 'pending'));
            }
        }

        if ($request->filled('q')) {
            $term = $request->string('q')->trim()->toString();
            $query->where(function ($builder) use ($term) {
                $builder->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereHas('requester', fn ($requesterQuery) => $requesterQuery->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('employee', fn ($employeeQuery) => $employeeQuery->where('full_name', 'like', "%{$term}%"));
            });
        }

        $requests = $query->get();

        return response()->json([
            'summary' => [
                'pending' => $requests->where('status', 'pending')->count(),
                'approved' => $requests->where('status', 'approved')->count(),
                'fulfilled' => $requests->where('status', 'fulfilled')->count(),
                'rejected' => $requests->where('status', 'rejected')->count(),
                'awaiting_my_approval' => $requests->filter(fn (WorkflowRequest $workflowRequest) => $this->isCurrentApprover($workflowRequest, $user->id))->count(),
            ],
            'requests' => $requests->map(fn (WorkflowRequest $workflowRequest) => $this->transformRequest($workflowRequest, $user)),
            'availableAssets' => $user->hasAnyRole(['admin', 'hr_manager'])
                ? Asset::query()
                    ->where('status', 'available')
                    ->orderBy('name')
                    ->get(['id', 'name', 'serial_number', 'category'])
                    ->map(fn (Asset $asset) => [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'serial_number' => $asset->serial_number,
                        'category' => $asset->category,
                    ])
                    ->values()
                : [],
            'templates' => WorkflowTemplate::query()
                ->orderBy('type')
                ->orderBy('name')
                ->get()
                ->map(fn (WorkflowTemplate $template) => $this->transformTemplate($template))
                ->values(),
            'canCreate' => $this->resolveEmployee($user, $tenantId) !== null,
            'isAdmin' => $user->hasAnyRole(['admin', 'hr_manager']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId = TenantContext::id();
        $user = $request->user();
        $employee = $this->resolveEmployee($user, $tenantId);

        abort_unless($employee, 422, 'No employee profile is linked to this user.');

        $validated = $request->validate([
            'workflow_template_id' => ['nullable', 'integer'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(WorkflowRequest::types()))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1500'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'details' => ['nullable', 'array'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        $template = $this->resolveTemplate($validated['workflow_template_id'] ?? null, $validated['type']);
        $details = $this->normalizeDetails($validated['type'], $validated['details'] ?? [], $validated['amount'] ?? null);
        $this->validateWorkflowByType($validated['type'], $validated, $details);

        $attachment = $request->file('attachment');
        $attachmentPayload = $attachment
            ? [
                'attachment_path' => $attachment->store('workflow-attachments/' . $tenantId, 'local'),
                'attachment_name' => $attachment->getClientOriginalName(),
                'attachment_size' => $attachment->getSize(),
                'attachment_mime_type' => $attachment->getMimeType(),
            ]
            : [];

        $workflowRequest = WorkflowRequest::create([
            'tenant_id' => $tenantId,
            'requester_user_id' => $user->id,
            'employee_id' => $employee->id,
            'workflow_template_id' => $template?->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'] ?? null,
            'details' => $details,
            'status' => 'pending',
            'submitted_at' => now(),
            ...$attachmentPayload,
        ]);

        $approverSteps = $template
            ? $this->resolveApproverIdsFromTemplate($template, $employee, $tenantId, $user->id)
            : $this->resolveApproverIds($employee, $tenantId, $user->id);

        foreach ($approverSteps as $step) {
            WorkflowApproval::create([
                'workflow_request_id' => $workflowRequest->id,
                'tenant_id' => $tenantId,
                'approver_user_id' => $step['approver_user_id'],
                'comment' => $step['step_label'] ? '[Step] ' . $step['step_label'] : null,
            ]);
        }

        $workflowRequest->load(['requester:id,name,email', 'employee:id,full_name', 'template:id,name,type', 'approvals.approver:id,name']);

        $approvers = User::query()
            ->whereIn('id', collect($this->currentPendingApprovals($workflowRequest))->pluck('approver_user_id')->all())
            ->get();

        if ($approvers->isNotEmpty()) {
            Notification::send($approvers, new WorkflowSubmitted($workflowRequest, $user->name));
        }

        ActivityLogger::log('workflow.requested', $workflowRequest, [
            'type' => $workflowRequest->type,
            'title' => $workflowRequest->title,
            'template_id' => $template?->id,
            'approver_count' => count($approverSteps),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request submitted for approval.',
            'request' => $this->transformRequest($workflowRequest, $user),
        ]);
    }

    public function show(WorkflowRequest $workflow): JsonResponse
    {
        $user = request()->user();
        $this->authorizeAccess($workflow, $user);

        $workflow->load(['requester:id,name,email', 'employee:id,full_name', 'template:id,name,type', 'approvals.approver:id,name']);

        return response()->json([
            'request' => $this->transformRequest($workflow, $user, true),
        ]);
    }

    public function downloadAttachment(WorkflowRequest $workflow)
    {
        $user = request()->user();
        $this->authorizeAccess($workflow, $user);

        abort_unless($workflow->attachment_path, 404);

        return Storage::disk('local')->download(
            $workflow->attachment_path,
            $workflow->attachment_name ?? basename($workflow->attachment_path)
        );
    }

    public function approve(Request $request, WorkflowRequest $workflow): JsonResponse
    {
        $user = $request->user();
        $approval = $this->resolvePendingApproval($workflow, $user->id);

        $validated = $request->validate([
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $approval->update([
            'decision' => 'approved',
            'comment' => $this->mergeApprovalComment($approval->comment, $validated['comment'] ?? null),
            'acted_at' => now(),
        ]);

        $remainingPending = $workflow->approvals()->where('decision', 'pending')->count();
        if ($remainingPending === 0) {
            $workflow->update([
                'status' => 'approved',
                'resolved_at' => now(),
                'last_action_by' => $user->id,
            ]);

            $this->applyApprovedWorkflow($workflow);
        } else {
            $workflow->update(['last_action_by' => $user->id]);
        }

        $workflow->load(['requester:id,name,email', 'employee:id,full_name', 'template:id,name,type', 'approvals.approver:id,name']);

        if (in_array($workflow->status, ['approved', 'fulfilled'], true) && $workflow->requester) {
            $workflow->requester->notify(new WorkflowApproved($workflow));
        }

        if ($workflow->status === 'pending') {
            $nextApprovers = User::query()
                ->whereIn('id', collect($this->currentPendingApprovals($workflow))->pluck('approver_user_id')->all())
                ->get();

            if ($nextApprovers->isNotEmpty()) {
                Notification::send($nextApprovers, new WorkflowSubmitted($workflow, $workflow->requester?->name ?? 'Requester'));
            }
        }

        ActivityLogger::log('workflow.approved', $workflow, [
            'approval_id' => $approval->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => $workflow->status === 'approved'
                ? 'Request approved.'
                : 'Approval recorded. Waiting for remaining approvers.',
            'request' => $this->transformRequest($workflow, $user),
        ]);
    }

    public function reject(Request $request, WorkflowRequest $workflow): JsonResponse
    {
        $user = $request->user();
        $approval = $this->resolvePendingApproval($workflow, $user->id);

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $approval->update([
            'decision' => 'rejected',
            'comment' => $this->mergeApprovalComment($approval->comment, $validated['comment']),
            'acted_at' => now(),
        ]);

        $workflow->approvals()
            ->where('decision', 'pending')
            ->update([
                'decision' => 'cancelled',
                'acted_at' => now(),
                'updated_at' => now(),
            ]);

        $workflow->update([
            'status' => 'rejected',
            'resolved_at' => now(),
            'last_action_by' => $user->id,
        ]);

        $workflow->load(['requester:id,name,email', 'employee:id,full_name', 'template:id,name,type', 'approvals.approver:id,name']);

        if ($workflow->requester) {
            $workflow->requester->notify(new WorkflowRejected($workflow, $validated['comment']));
        }

        ActivityLogger::log('workflow.rejected', $workflow, [
            'approval_id' => $approval->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request rejected.',
            'request' => $this->transformRequest($workflow, $user),
        ]);
    }

    public function fulfillAsset(Request $request, WorkflowRequest $workflow): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->hasAnyRole(['admin', 'hr_manager']), 403, 'Unauthorized.');
        abort_unless($workflow->type === 'asset-request', 422, 'Only asset requests can be fulfilled.');
        abort_unless($workflow->status === 'approved', 422, 'Only approved asset requests can be fulfilled.');
        abort_unless($workflow->employee_id, 422, 'Asset request is not linked to an employee.');

        $validated = $request->validate([
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $asset = Asset::query()
            ->where('status', 'available')
            ->findOrFail($validated['asset_id']);

        $asset->update([
            'employee_id' => $workflow->employee_id,
            'status' => 'assigned',
            'assigned_at' => now(),
            'notes' => trim(($asset->notes ? $asset->notes . PHP_EOL : '') . 'Assigned via workflow #' . $workflow->id),
        ]);

        $details = $workflow->details ?? [];
        $details['fulfilled_asset_id'] = $asset->id;
        $details['fulfilled_asset_name'] = $asset->name;
        $details['fulfilled_at'] = now()->toDateTimeString();
        if (! empty($validated['comment'])) {
            $details['fulfillment_note'] = $validated['comment'];
        }

        $workflow->update([
            'status' => 'fulfilled',
            'details' => $details,
            'last_action_by' => $user->id,
            'resolved_at' => now(),
        ]);

        $workflow->load(['requester:id,name,email', 'employee:id,full_name', 'template:id,name,type', 'approvals.approver:id,name']);

        if ($workflow->requester) {
            $workflow->requester->notify(new WorkflowFulfilled($workflow, $asset->name));
        }

        ActivityLogger::log('workflow.fulfilled', $workflow, [
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset request fulfilled and inventory assigned.',
            'request' => $this->transformRequest($workflow, $user),
        ]);
    }

    public function cancel(Request $request, WorkflowRequest $workflow): JsonResponse
    {
        $user = $request->user();
        abort_unless((int) $workflow->requester_user_id === (int) $user->id, 403, 'Unauthorized.');
        abort_unless($workflow->status === 'pending', 422, 'Only pending requests can be cancelled.');

        $workflow->approvals()
            ->where('decision', 'pending')
            ->update([
                'decision' => 'cancelled',
                'acted_at' => now(),
                'updated_at' => now(),
            ]);

        $workflow->update([
            'status' => 'cancelled',
            'resolved_at' => now(),
            'last_action_by' => $user->id,
        ]);

        ActivityLogger::log('workflow.cancelled', $workflow, [
            'requester_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Workflow request cancelled.',
            'request' => $this->transformRequest($workflow->fresh(['requester:id,name,email', 'employee:id,full_name', 'template:id,name,type', 'approvals.approver:id,name']), $user),
        ]);
    }

    public function resubmit(Request $request, WorkflowRequest $workflow): JsonResponse
    {
        $tenantId = TenantContext::id();
        $user = $request->user();
        abort_unless((int) $workflow->requester_user_id === (int) $user->id, 403, 'Unauthorized.');
        abort_unless(in_array($workflow->status, ['rejected', 'cancelled'], true), 422, 'Only rejected or cancelled workflows can be resubmitted.');

        $validated = $request->validate([
            'workflow_template_id' => ['nullable', 'integer'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(WorkflowRequest::types()))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1500'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'details' => ['nullable', 'array'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        $employee = $this->resolveEmployee($user, $tenantId);
        abort_unless($employee, 422, 'No employee profile is linked to this user.');

        $template = $this->resolveTemplate($validated['workflow_template_id'] ?? null, $validated['type']);
        $details = $this->normalizeDetails($validated['type'], $validated['details'] ?? [], $validated['amount'] ?? null);
        $this->validateWorkflowByType($validated['type'], $validated, $details);

        $attachment = $request->file('attachment');
        $attachmentPayload = $attachment
            ? [
                'attachment_path' => $attachment->store('workflow-attachments/' . $tenantId, 'local'),
                'attachment_name' => $attachment->getClientOriginalName(),
                'attachment_size' => $attachment->getSize(),
                'attachment_mime_type' => $attachment->getMimeType(),
            ]
            : [
                'attachment_path' => $workflow->attachment_path,
                'attachment_name' => $workflow->attachment_name,
                'attachment_size' => $workflow->attachment_size,
                'attachment_mime_type' => $workflow->attachment_mime_type,
            ];

        $workflow->approvals()->delete();

        $workflow->update([
            'employee_id' => $employee->id,
            'workflow_template_id' => $template?->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'] ?? null,
            'details' => $details,
            'status' => 'pending',
            'submitted_at' => now(),
            'resolved_at' => null,
            'last_action_by' => $user->id,
            ...$attachmentPayload,
        ]);

        $approverSteps = $template
            ? $this->resolveApproverIdsFromTemplate($template, $employee, $tenantId, $user->id)
            : $this->resolveApproverIds($employee, $tenantId, $user->id);

        foreach ($approverSteps as $step) {
            WorkflowApproval::create([
                'workflow_request_id' => $workflow->id,
                'tenant_id' => $tenantId,
                'approver_user_id' => $step['approver_user_id'],
                'comment' => $step['step_label'] ? '[Step] ' . $step['step_label'] : null,
            ]);
        }

        $workflow->load(['requester:id,name,email', 'employee:id,full_name', 'template:id,name,type', 'approvals.approver:id,name']);

        $approvers = User::query()
            ->whereIn('id', collect($this->currentPendingApprovals($workflow))->pluck('approver_user_id')->all())
            ->get();

        if ($approvers->isNotEmpty()) {
            Notification::send($approvers, new WorkflowSubmitted($workflow, $user->name));
        }

        ActivityLogger::log('workflow.resubmitted', $workflow, [
            'type' => $workflow->type,
            'title' => $workflow->title,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Workflow request resubmitted.',
            'request' => $this->transformRequest($workflow, $user),
        ]);
    }

    public function storeTemplate(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->hasAnyRole(['admin', 'hr_manager']), 403, 'Unauthorized.');

        $validated = $this->validateTemplatePayload($request);

        $template = WorkflowTemplate::create([
            'tenant_id' => TenantContext::id(),
            'type' => $validated['type'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'default_title' => $validated['default_title'] ?? null,
            'default_description' => $validated['default_description'] ?? null,
            'approval_steps' => $this->normalizeTemplateSteps($validated['approval_steps'] ?? []),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        ActivityLogger::log('workflow.template.created', $template, [
            'type' => $template->type,
            'name' => $template->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Workflow template saved.',
            'template' => $this->transformTemplate($template),
        ]);
    }

    public function updateTemplate(Request $request, WorkflowTemplate $template): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->hasAnyRole(['admin', 'hr_manager']), 403, 'Unauthorized.');

        $validated = $this->validateTemplatePayload($request);

        $template->update([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'default_title' => $validated['default_title'] ?? null,
            'default_description' => $validated['default_description'] ?? null,
            'approval_steps' => $this->normalizeTemplateSteps($validated['approval_steps'] ?? []),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        ActivityLogger::log('workflow.template.updated', $template, [
            'type' => $template->type,
            'name' => $template->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Workflow template updated.',
            'template' => $this->transformTemplate($template),
        ]);
    }

    public function archiveTemplate(Request $request, WorkflowTemplate $template): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->hasAnyRole(['admin', 'hr_manager']), 403, 'Unauthorized.');

        $template->update(['is_active' => false]);

        ActivityLogger::log('workflow.template.archived', $template, [
            'type' => $template->type,
            'name' => $template->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Workflow template archived.',
            'template' => $this->transformTemplate($template),
        ]);
    }

    private function validateTemplatePayload(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(WorkflowRequest::types()))],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1500'],
            'default_title' => ['nullable', 'string', 'max:255'],
            'default_description' => ['nullable', 'string', 'max:1500'],
            'is_active' => ['nullable', 'boolean'],
            'approval_steps' => ['nullable', 'array', 'min:1'],
            'approval_steps.*.role' => ['required_with:approval_steps', 'string', Rule::in(['manager', 'hr_manager', 'admin'])],
            'approval_steps.*.label' => ['nullable', 'string', 'max:100'],
        ]);
    }

    private function resolveEmployee(User $user, ?int $tenantId): ?Employee
    {
        return Employee::query()
            ->where('email', $user->email)
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->first();
    }

    private function resolveTemplate(?int $templateId, string $type): ?WorkflowTemplate
    {
        if (! $templateId) {
            return null;
        }

        $template = WorkflowTemplate::query()->find($templateId);
        abort_unless($template, 422, 'Selected workflow template could not be found.');
        abort_unless($template->type === $type, 422, 'Selected workflow template does not match the request type.');
        abort_unless($template->is_active, 422, 'Selected workflow template is inactive.');

        return $template;
    }

    private function resolveApproverIds(Employee $employee, ?int $tenantId, int $requesterUserId): array
    {
        $approverIds = collect();

        if ($employee->manager?->email) {
            $managerUserId = User::query()
                ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
                ->where('email', $employee->manager->email)
                ->value('id');

            if ($managerUserId) {
                $approverIds->push([
                    'approver_user_id' => (int) $managerUserId,
                    'step_label' => 'Manager Review',
                ]);
            }
        }

        $adminApproverIds = User::query()
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['admin', 'hr_manager']))
            ->pluck('id');

        return $approverIds
            ->merge($adminApproverIds->map(fn ($id) => [
                'approver_user_id' => (int) $id,
                'step_label' => 'HR Approval',
            ]))
            ->reject(fn ($step) => (int) $step['approver_user_id'] === $requesterUserId)
            ->unique('approver_user_id')
            ->values()
            ->all();
    }

    private function resolveApproverIdsFromTemplate(WorkflowTemplate $template, Employee $employee, ?int $tenantId, int $requesterUserId): array
    {
        $steps = collect($template->approval_steps ?? [])
            ->filter(fn ($step) => filled($step['role'] ?? null))
            ->values();

        if ($steps->isEmpty()) {
            return $this->resolveApproverIds($employee, $tenantId, $requesterUserId);
        }

        $resolved = collect();

        foreach ($steps as $step) {
            $label = $step['label'] ?? ucfirst(str_replace('_', ' ', (string) $step['role']));

            if (($step['role'] ?? null) === 'manager') {
                if ($employee->manager?->email) {
                    $managerUserId = User::query()
                        ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
                        ->where('email', $employee->manager->email)
                        ->value('id');

                    if ($managerUserId && (int) $managerUserId !== $requesterUserId) {
                        $resolved->push([
                            'approver_user_id' => (int) $managerUserId,
                            'step_label' => $label,
                        ]);
                    }
                }

                continue;
            }

            $roleName = ($step['role'] ?? null) === 'admin' ? 'admin' : 'hr_manager';
            $roleUserIds = User::query()
                ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
                ->whereHas('roles', fn ($query) => $query->where('name', $roleName))
                ->pluck('id');

            foreach ($roleUserIds as $roleUserId) {
                if ((int) $roleUserId === $requesterUserId) {
                    continue;
                }

                $resolved->push([
                    'approver_user_id' => (int) $roleUserId,
                    'step_label' => $label,
                ]);
            }
        }

        return $resolved
            ->unique(fn ($step) => $step['approver_user_id'] . ':' . $step['step_label'])
            ->values()
            ->all();
    }

    private function authorizeAccess(WorkflowRequest $workflow, User $user): void
    {
        if ($user->hasAnyRole(['admin', 'hr_manager'])) {
            return;
        }

        $isRequester = (int) $workflow->requester_user_id === (int) $user->id;
        $isApprover = $workflow->approvals()->where('approver_user_id', $user->id)->exists();

        abort_unless($isRequester || $isApprover, 403, 'Unauthorized.');
    }

    private function resolvePendingApproval(WorkflowRequest $workflow, int $userId): WorkflowApproval
    {
        $approval = $this->currentPendingApprovals($workflow)
            ->first(fn (WorkflowApproval $workflowApproval) => (int) $workflowApproval->approver_user_id === $userId);

        abort_unless($approval, 403, 'No pending approval is assigned to you.');

        return $approval;
    }

    private function transformRequest(WorkflowRequest $workflowRequest, User $viewer, bool $withDetails = false): array
    {
        $fulfilledAsset = $withDetails ? $this->resolveFulfilledAssetSummary($workflowRequest) : null;
        $pendingApproverNames = $this->currentPendingApprovals($workflowRequest)
            ->map(function (WorkflowApproval $approval) {
                $label = $this->extractStepLabel($approval->comment);
                $name = $approval->approver?->name ?? 'Unknown approver';

                return $label ? $name . ' (' . $label . ')' : $name;
            })
            ->filter()
            ->values();

        return [
            'id' => $workflowRequest->id,
            'type' => $workflowRequest->type,
            'type_label' => WorkflowRequest::types()[$workflowRequest->type] ?? ucfirst($workflowRequest->type),
            'template_id' => $workflowRequest->workflow_template_id,
            'template_name' => $workflowRequest->template?->name,
            'title' => $workflowRequest->title,
            'description' => $workflowRequest->description,
            'amount' => $workflowRequest->amount !== null ? number_format((float) $workflowRequest->amount, 2) : null,
            'amount_value' => $workflowRequest->amount !== null ? (float) $workflowRequest->amount : null,
            'details_preview' => $this->buildDetailsPreview($workflowRequest->details ?? []),
            'attachment_name' => $workflowRequest->attachment_name,
            'attachment_size' => $workflowRequest->attachment_size,
            'has_attachment' => ! empty($workflowRequest->attachment_path),
            'can_fulfill' => $viewer->hasAnyRole(['admin', 'hr_manager'])
                && $workflowRequest->type === 'asset-request'
                && $workflowRequest->status === 'approved',
            'can_cancel' => (int) $workflowRequest->requester_user_id === (int) $viewer->id
                && $workflowRequest->status === 'pending',
            'can_resubmit' => (int) $workflowRequest->requester_user_id === (int) $viewer->id
                && in_array($workflowRequest->status, ['rejected', 'cancelled'], true),
            'status' => $workflowRequest->status,
            'requester_name' => $workflowRequest->requester?->name,
            'employee_name' => $workflowRequest->employee?->full_name,
            'submitted_at' => $workflowRequest->submitted_at?->format('d M Y, h:i A'),
            'submitted_at_short' => $workflowRequest->submitted_at?->diffForHumans(),
            'resolved_at' => $workflowRequest->resolved_at?->format('d M Y, h:i A'),
            'pending_approvers' => $pendingApproverNames,
            'can_approve' => $this->isCurrentApprover($workflowRequest, $viewer->id),
            'is_requester' => (int) $workflowRequest->requester_user_id === (int) $viewer->id,
            'timeline' => $withDetails ? $workflowRequest->approvals->map(function (WorkflowApproval $approval) {
                return [
                    'id' => $approval->id,
                    'approver_name' => $approval->approver?->name ?? 'Unknown approver',
                    'decision' => $approval->decision,
                    'step_label' => $this->extractStepLabel($approval->comment),
                    'comment' => $this->extractDecisionComment($approval->comment),
                    'acted_at' => $approval->acted_at?->format('d M Y, h:i A'),
                ];
            })->values() : [],
            'details' => $withDetails ? ($workflowRequest->details ?? []) : [],
            'fulfilled_asset' => $fulfilledAsset,
            'attachment' => $withDetails && $workflowRequest->attachment_path ? [
                'name' => $workflowRequest->attachment_name,
                'size' => $workflowRequest->attachment_size,
                'mime_type' => $workflowRequest->attachment_mime_type,
                'download_url' => route('workflows.attachment', $workflowRequest),
            ] : null,
        ];
    }

    private function transformTemplate(WorkflowTemplate $template): array
    {
        return [
            'id' => $template->id,
            'type' => $template->type,
            'type_label' => WorkflowRequest::types()[$template->type] ?? ucfirst($template->type),
            'name' => $template->name,
            'description' => $template->description,
            'default_title' => $template->default_title,
            'default_description' => $template->default_description,
            'approval_steps' => collect($template->approval_steps ?? [])->values(),
            'approval_summary' => collect($template->approval_steps ?? [])
                ->map(function ($step) {
                    $label = $step['label'] ?? ucfirst(str_replace('_', ' ', (string) ($step['role'] ?? 'step')));
                    $role = ucfirst(str_replace('_', ' ', (string) ($step['role'] ?? 'step')));

                    return $label . ' - ' . $role;
                })
                ->values(),
            'is_active' => (bool) $template->is_active,
        ];
    }

    private function normalizeDetails(string $type, array $details, mixed $amount): array
    {
        if ($type === 'asset-request') {
            return array_filter([
                'asset_category' => isset($details['asset_category']) ? strtolower(trim((string) $details['asset_category'])) : null,
                'urgency' => isset($details['urgency']) ? strtolower(trim((string) $details['urgency'])) : null,
                'needed_by' => $details['needed_by'] ?? null,
                'preferred_model' => isset($details['preferred_model']) ? trim((string) $details['preferred_model']) : null,
                'business_reason' => isset($details['business_reason']) ? trim((string) $details['business_reason']) : null,
            ], fn ($value) => $value !== null && $value !== '');
        }

        if ($type === 'asset-return') {
            return array_filter([
                'asset_id' => isset($details['asset_id']) && $details['asset_id'] !== '' ? (int) $details['asset_id'] : null,
                'asset_name' => isset($details['asset_name']) ? trim((string) $details['asset_name']) : null,
                'asset_category' => isset($details['asset_category']) ? strtolower(trim((string) $details['asset_category'])) : null,
                'serial_number' => isset($details['serial_number']) ? trim((string) $details['serial_number']) : null,
                'return_condition' => isset($details['return_condition']) ? strtolower(trim((string) $details['return_condition'])) : null,
                'requested_return_date' => $details['requested_return_date'] ?? null,
                'reason' => isset($details['reason']) ? trim((string) $details['reason']) : null,
            ], fn ($value) => $value !== null && $value !== '');
        }

        if ($type === 'asset-repair') {
            return array_filter([
                'asset_id' => isset($details['asset_id']) && $details['asset_id'] !== '' ? (int) $details['asset_id'] : null,
                'asset_name' => isset($details['asset_name']) ? trim((string) $details['asset_name']) : null,
                'asset_category' => isset($details['asset_category']) ? strtolower(trim((string) $details['asset_category'])) : null,
                'serial_number' => isset($details['serial_number']) ? trim((string) $details['serial_number']) : null,
                'issue_type' => isset($details['issue_type']) ? strtolower(trim((string) $details['issue_type'])) : null,
                'reported_condition' => isset($details['reported_condition']) ? strtolower(trim((string) $details['reported_condition'])) : null,
                'reported_at' => $details['reported_at'] ?? null,
                'reason' => isset($details['reason']) ? trim((string) $details['reason']) : null,
            ], fn ($value) => $value !== null && $value !== '');
        }

        if ($type === 'reimbursement') {
            return array_filter([
                'category' => isset($details['category']) ? strtolower(trim((string) $details['category'])) : null,
                'expense_date' => $details['expense_date'] ?? null,
                'merchant' => isset($details['merchant']) ? trim((string) $details['merchant']) : null,
                'receipt_reference' => isset($details['receipt_reference']) ? trim((string) $details['receipt_reference']) : null,
                'notes' => isset($details['notes']) ? trim((string) $details['notes']) : null,
                'claim_amount' => $amount !== null ? (float) $amount : null,
            ], fn ($value) => $value !== null && $value !== '');
        }

        if ($type === 'profile-change') {
            return array_filter([
                'field_name' => isset($details['field_name']) ? strtolower(trim((string) $details['field_name'])) : null,
                'current_value' => isset($details['current_value']) ? trim((string) $details['current_value']) : null,
                'requested_value' => isset($details['requested_value']) ? trim((string) $details['requested_value']) : null,
                'effective_from' => $details['effective_from'] ?? null,
                'reason' => isset($details['reason']) ? trim((string) $details['reason']) : null,
            ], fn ($value) => $value !== null && $value !== '');
        }

        if ($type === 'salary-change') {
            return array_filter([
                'change_type' => isset($details['change_type']) ? strtolower(trim((string) $details['change_type'])) : null,
                'requested_salary' => isset($details['requested_salary']) && $details['requested_salary'] !== '' ? (float) $details['requested_salary'] : null,
                'effective_from' => $details['effective_from'] ?? null,
                'justification' => isset($details['justification']) ? trim((string) $details['justification']) : null,
            ], fn ($value) => $value !== null && $value !== '');
        }

        if ($type === 'offboarding') {
            return array_filter([
                'last_working_day' => $details['last_working_day'] ?? null,
                'exit_type' => isset($details['exit_type']) ? strtolower(trim((string) $details['exit_type'])) : null,
                'handover_owner' => isset($details['handover_owner']) ? trim((string) $details['handover_owner']) : null,
                'reason' => isset($details['reason']) ? trim((string) $details['reason']) : null,
            ], fn ($value) => $value !== null && $value !== '');
        }

        return array_filter($details, fn ($value) => $value !== null && $value !== '');
    }

    private function validateWorkflowByType(string $type, array $validated, array $details): void
    {
        if ($type === 'asset-request') {
            validator(['details' => $details], [
                'details.asset_category' => ['required', 'string', 'in:' . implode(',', array_keys(Asset::categories()))],
                'details.urgency' => ['required', 'string', 'in:low,medium,high'],
                'details.needed_by' => ['nullable', 'date', 'after_or_equal:today'],
                'details.preferred_model' => ['nullable', 'string', 'max:255'],
                'details.business_reason' => ['required', 'string', 'max:1000'],
            ])->validate();

            return;
        }

        if ($type === 'asset-return') {
            validator(['details' => $details], [
                'details.asset_id' => ['required', 'integer', 'exists:assets,id'],
                'details.return_condition' => ['required', 'string', 'in:good,minor-issues,damaged,unknown'],
                'details.requested_return_date' => ['required', 'date', 'after_or_equal:today'],
                'details.reason' => ['required', 'string', 'max:1000'],
            ])->validate();

            $asset = $this->resolveEmployeeAssetForWorkflow((int) $details['asset_id'], $validated['employee_id'] ?? null);
            if ($asset->status !== 'assigned') {
                throw ValidationException::withMessages([
                    'details.asset_id' => 'Only assigned assets can be returned through workflow.',
                ]);
            }

            return;
        }

        if ($type === 'asset-repair') {
            validator(['details' => $details], [
                'details.asset_id' => ['required', 'integer', 'exists:assets,id'],
                'details.issue_type' => ['required', 'string', 'in:hardware,software,accessory,wear-tear,other'],
                'details.reported_condition' => ['required', 'string', 'in:working,partially-working,not-working,damaged'],
                'details.reported_at' => ['required', 'date', 'before_or_equal:today'],
                'details.reason' => ['required', 'string', 'max:1000'],
            ])->validate();

            $asset = $this->resolveEmployeeAssetForWorkflow((int) $details['asset_id'], $validated['employee_id'] ?? null);
            if (! in_array($asset->status, ['assigned', 'maintenance'], true)) {
                throw ValidationException::withMessages([
                    'details.asset_id' => 'Only assigned assets can be reported for repair.',
                ]);
            }

            return;
        }

        if ($type === 'profile-change') {
            validator(['details' => $details], [
                'details.field_name' => ['required', 'string', Rule::in($this->allowedProfileChangeFields())],
                'details.current_value' => ['nullable', 'string', 'max:255'],
                'details.requested_value' => ['required', 'string', 'max:255'],
                'details.effective_from' => ['nullable', 'date', 'after_or_equal:today'],
                'details.reason' => ['required', 'string', 'max:1000'],
            ])->validate();

            return;
        }

        if ($type === 'salary-change') {
            validator(['details' => $details], [
                'details.change_type' => ['required', 'string', 'in:raise,correction,promotion,adjustment'],
                'details.requested_salary' => ['required', 'numeric', 'min:1'],
                'details.effective_from' => ['required', 'date', 'after_or_equal:today'],
                'details.justification' => ['required', 'string', 'max:1000'],
            ])->validate();

            return;
        }

        if ($type === 'offboarding') {
            validator(['details' => $details], [
                'details.last_working_day' => ['required', 'date'],
                'details.exit_type' => ['required', 'string', 'in:resignation,termination,retirement,contract-end'],
                'details.handover_owner' => ['nullable', 'string', 'max:255'],
                'details.reason' => ['required', 'string', 'max:1000'],
            ])->validate();

            return;
        }

        if ($type !== 'reimbursement') {
            return;
        }

        validator(['amount' => $validated['amount'] ?? null, 'details' => $details], [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'details.category' => ['required', 'string', 'max:100'],
            'details.expense_date' => ['required', 'date', 'before_or_equal:today'],
            'details.merchant' => ['required', 'string', 'max:255'],
            'details.receipt_reference' => ['nullable', 'string', 'max:255'],
            'details.notes' => ['nullable', 'string', 'max:1000'],
        ])->validate();

        $activePolicy = ReimbursementPolicy::query()->active()->effectiveOn()->first();

        if (! $activePolicy) {
            return;
        }

        $category = $details['category'] ?? null;
        $amount = (float) ($validated['amount'] ?? 0);
        $allowedCategories = collect($activePolicy->allowed_categories ?? [])->map(fn ($item) => strtolower((string) $item));

        if ($allowedCategories->isNotEmpty() && $category && ! $allowedCategories->contains($category)) {
            throw ValidationException::withMessages([
                'details.category' => 'This reimbursement category is not allowed by the active policy.',
            ]);
        }

        if ($activePolicy->single_claim_limit !== null && $amount > (float) $activePolicy->single_claim_limit) {
            throw ValidationException::withMessages([
                'amount' => 'This reimbursement exceeds the active single claim limit.',
            ]);
        }

        if ($activePolicy->receipt_required && empty($details['receipt_reference'])) {
            throw ValidationException::withMessages([
                'details.receipt_reference' => 'Receipt reference is required by the active reimbursement policy.',
            ]);
        }
    }

    private function applyApprovedWorkflow(WorkflowRequest $workflow): void
    {
        $details = $workflow->details ?? [];
        $mutated = false;
        $employee = $workflow->employee;
        $asset = null;

        if ($workflow->type === 'profile-change' && $employee) {
            $field = $details['field_name'] ?? null;
            if ($field && in_array($field, $this->allowedProfileChangeFields(), true)) {
                $employee->update([$field => $details['requested_value'] ?? null]);
                $details['applied_field'] = $field;
                $details['applied_value'] = $details['requested_value'] ?? null;
                $mutated = true;
            }
        }

        if ($workflow->type === 'salary-change' && $employee && ! empty($details['requested_salary'])) {
            $newSalary = (float) $details['requested_salary'];
            $employee->update(['salary' => $newSalary]);

            $payStructure = \App\Models\PayStructure::query()
                ->where('employee_id', $employee->id)
                ->first();

            if ($payStructure) {
                $payStructure->update(['base_salary' => $newSalary]);
            }

            $details['applied_salary'] = $newSalary;
            $mutated = true;
        }

        if ($workflow->type === 'offboarding' && $employee) {
            $employee->update(['status' => 'inactive']);
            $details['offboarding_applied'] = true;
            $mutated = true;
        }

        // ── Reimbursement → queue as a payroll adjustment ──────────────────
        if ($workflow->type === 'reimbursement' && $employee && $workflow->amount > 0) {
            $category = $details['category'] ?? 'Reimbursement';
            $label = $workflow->title ?: ucfirst($category) . ' Reimbursement';

            \App\Models\PayrollAdjustment::create([
                'tenant_id'           => $workflow->tenant_id,
                'employee_id'         => $employee->id,
                'workflow_request_id' => $workflow->id,
                'label'               => $label,
                'type'                => 'addition',
                'amount'              => $workflow->amount,
                'status'              => 'pending',
            ]);

            $details['payroll_adjustment_queued'] = true;
            $mutated = true;
        }

        if ($workflow->type === 'asset-return' && ! empty($details['asset_id'])) {
            $asset = $this->resolveWorkflowAsset($workflow, (int) $details['asset_id']);
            if ($asset) {
                $asset->update([
                    'employee_id' => null,
                    'status' => 'available',
                    'assigned_at' => null,
                    'notes' => trim(($asset->notes ? $asset->notes . PHP_EOL : '') . 'Returned via workflow #' . $workflow->id),
                ]);

                $details['asset_action'] = 'returned';
                $details['asset_status'] = 'available';
                $mutated = true;
            }
        }

        if ($workflow->type === 'asset-repair' && ! empty($details['asset_id'])) {
            $asset = $this->resolveWorkflowAsset($workflow, (int) $details['asset_id']);
            if ($asset) {
                $asset->update([
                    'status' => 'maintenance',
                    'notes' => trim(($asset->notes ? $asset->notes . PHP_EOL : '') . 'Repair requested via workflow #' . $workflow->id),
                ]);

                $details['asset_action'] = 'repair-opened';
                $details['asset_status'] = 'maintenance';
                $mutated = true;
            }
        }

        if (! $mutated) {
            return;
        }

        $details['applied_at'] = now()->toDateTimeString();

        $workflow->update([
            'status' => 'fulfilled',
            'details' => $details,
            'resolved_at' => now(),
        ]);

        ActivityLogger::log('workflow.applied', $workflow, [
            'type' => $workflow->type,
            'employee_id' => $employee?->id,
            'asset_id' => $asset?->id,
        ]);
    }

    private function allowedProfileChangeFields(): array
    {
        return [
            'phone',
            'address',
            'city',
            'state',
            'country',
            'zip_code',
            'personal_email',
            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_relationship',
            'bank_name',
            'bank_account_number',
            'bank_ifsc',
            'linkedin_url',
            'pronouns',
            'bio',
            'marital_status',
            'nationality',
            'food_preference',
            'health_issues',
        ];
    }

    private function currentPendingApprovals(WorkflowRequest $workflow): \Illuminate\Support\Collection
    {
        $workflow->loadMissing('approvals.approver');
        $pendingApprovals = $workflow->approvals
            ->where('decision', 'pending')
            ->sortBy('id')
            ->values();

        if ($pendingApprovals->isEmpty()) {
            return collect();
        }

        $firstLabel = $this->extractStepLabel($pendingApprovals->first()->comment);

        return $pendingApprovals
            ->takeWhile(fn (WorkflowApproval $approval) => $this->extractStepLabel($approval->comment) === $firstLabel)
            ->values();
    }

    private function isCurrentApprover(WorkflowRequest $workflow, int $userId): bool
    {
        return $this->currentPendingApprovals($workflow)
            ->contains(fn (WorkflowApproval $approval) => (int) $approval->approver_user_id === $userId);
    }

    private function buildDetailsPreview(array $details): ?string
    {
        if (! empty($details['asset_category'])) {
            if (! empty($details['asset_name']) || ! empty($details['asset_id'])) {
                return implode(' - ', array_filter([
                    $details['asset_name'] ?? ('Asset #' . $details['asset_id']),
                    ! empty($details['issue_type']) ? ucfirst(str_replace('-', ' ', (string) $details['issue_type'])) : null,
                    ! empty($details['return_condition']) ? ucfirst(str_replace('-', ' ', (string) $details['return_condition'])) : null,
                    $details['requested_return_date'] ?? ($details['reported_at'] ?? null),
                ]));
            }

            return implode(' - ', array_filter([
                ucfirst(str_replace('-', ' ', (string) $details['asset_category'])),
                ! empty($details['urgency']) ? ucfirst((string) $details['urgency']) : null,
                $details['needed_by'] ?? null,
            ]));
        }

        if (! empty($details['field_name'])) {
            return implode(' - ', array_filter([
                ucfirst(str_replace('_', ' ', (string) $details['field_name'])),
                $details['requested_value'] ?? null,
                $details['effective_from'] ?? null,
            ]));
        }

        if (! empty($details['requested_salary'])) {
            return implode(' - ', array_filter([
                ucfirst((string) ($details['change_type'] ?? 'adjustment')),
                'INR ' . number_format((float) $details['requested_salary'], 2),
                $details['effective_from'] ?? null,
            ]));
        }

        if (! empty($details['last_working_day'])) {
            return implode(' - ', array_filter([
                ucfirst(str_replace('-', ' ', (string) ($details['exit_type'] ?? 'offboarding'))),
                $details['last_working_day'],
                $details['handover_owner'] ?? null,
            ]));
        }

        $parts = [];

        if (! empty($details['category'])) {
            $parts[] = ucfirst((string) $details['category']);
        }

        if (! empty($details['merchant'])) {
            $parts[] = (string) $details['merchant'];
        }

        if (! empty($details['expense_date'])) {
            $parts[] = (string) $details['expense_date'];
        }

        return $parts ? implode(' - ', $parts) : null;
    }

    private function resolveFulfilledAssetSummary(WorkflowRequest $workflowRequest): ?array
    {
        $details = $workflowRequest->details ?? [];
        $assetId = $details['fulfilled_asset_id'] ?? null;

        if (! $assetId) {
            return null;
        }

        $asset = Asset::query()->find($assetId);

        return [
            'id' => (int) $assetId,
            'name' => $asset?->name ?? ($details['fulfilled_asset_name'] ?? 'Assigned Asset'),
            'serial_number' => $asset?->serial_number,
            'category' => $asset?->category,
            'category_label' => $asset?->category
                ? (Asset::categories()[$asset->category] ?? ucfirst(str_replace('-', ' ', $asset->category)))
                : null,
            'assigned_at' => $asset?->assigned_at?->format('d M Y'),
            'assigned_to' => $workflowRequest->employee?->full_name,
            'fulfillment_note' => $details['fulfillment_note'] ?? null,
            'fulfilled_at' => ! empty($details['fulfilled_at']) ? (string) $details['fulfilled_at'] : null,
        ];
    }

    private function resolveEmployeeAssetForWorkflow(int $assetId, mixed $employeeId): Asset
    {
        $resolvedEmployeeId = $employeeId ? (int) $employeeId : null;

        if (! $resolvedEmployeeId) {
            $resolvedEmployeeId = Employee::query()
                ->where('tenant_id', TenantContext::id())
                ->where('email', request()->user()?->email)
                ->value('id');
        }

        if (! $resolvedEmployeeId) {
            throw ValidationException::withMessages([
                'details.asset_id' => 'No employee record is linked to this account.',
            ]);
        }

        $asset = Asset::query()
            ->where('tenant_id', TenantContext::id())
            ->where('id', $assetId)
            ->where('employee_id', $resolvedEmployeeId)
            ->first();

        if (! $asset) {
            throw ValidationException::withMessages([
                'details.asset_id' => 'This asset is not assigned to the current employee.',
            ]);
        }

        return $asset;
    }

    private function resolveWorkflowAsset(WorkflowRequest $workflow, int $assetId): ?Asset
    {
        return Asset::query()
            ->where('tenant_id', $workflow->tenant_id)
            ->where('id', $assetId)
            ->first();
    }
    private function normalizeTemplateSteps(array $steps): array
    {
        return collect($steps)
            ->map(fn ($step) => [
                'role' => $step['role'] ?? 'manager',
                'label' => $step['label'] ?? null,
            ])
            ->filter(fn ($step) => filled($step['role']))
            ->values()
            ->all();
    }

    private function mergeApprovalComment(?string $existingComment, ?string $newComment): ?string
    {
        $stepLabel = $this->extractStepLabel($existingComment);
        $decisionComment = trim((string) $newComment);

        if ($stepLabel && $decisionComment !== '') {
            return '[Step] ' . $stepLabel . PHP_EOL . $decisionComment;
        }

        if ($stepLabel) {
            return '[Step] ' . $stepLabel;
        }

        return $decisionComment !== '' ? $decisionComment : null;
    }

    private function extractStepLabel(?string $comment): ?string
    {
        if (! $comment || ! str_starts_with($comment, '[Step] ')) {
            return null;
        }

        $parts = preg_split("/\r\n|\n|\r/", $comment, 2);

        return isset($parts[0]) ? trim(str_replace('[Step] ', '', $parts[0])) : null;
    }

    private function extractDecisionComment(?string $comment): ?string
    {
        if (! $comment) {
            return null;
        }

        if (! str_starts_with($comment, '[Step] ')) {
            return $comment;
        }

        $parts = preg_split("/\r\n|\n|\r/", $comment, 2);

        return isset($parts[1]) ? trim($parts[1]) : null;
    }
}

