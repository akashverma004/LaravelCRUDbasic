<?php

namespace App\Livewire\Workflows;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\User;
use App\Models\WorkflowApproval;
use App\Models\WorkflowRequest;
use App\Models\WorkflowTemplate;
use App\Notifications\WorkflowApproved;
use App\Notifications\WorkflowFulfilled;
use App\Notifications\WorkflowRejected;
use App\Notifications\WorkflowSubmitted;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Workflow Hub - PeopleFlow HRMS')]
class WorkflowHub extends Component
{
    use WithPagination, WithFileUploads;

    #[Url]
    public $view = 'inbox'; // inbox, sent, templates
    #[Url]
    public $search = '';
    #[Url]
    public $type = 'all';
    #[Url]
    public $status = 'all';

    // State for Modals
    public bool $showRequestModal = false;
    public bool $showDetailsModal = false;
    public bool $showTemplateModal = false;
    public bool $showFulfillmentModal = false;

    // Selected Request / Template
    public $selectedRequestId;
    public $selectedTemplateId;

    // Request Form
    public $requestType = 'general';
    public $requestTemplateId = '';
    public $requestTitle = '';
    public $requestDescription = '';
    public $requestAmount = '';
    public $requestAttachment;
    public array $requestDetails = [];

    // Template Form
    public $templateName = '';
    public $templateType = 'general';
    public $templateDescription = '';
    public $templateDefaultTitle = '';
    public $templateDefaultDescription = '';
    public array $templateSteps = [];
    public bool $templateIsActive = true;

    // Fulfillment
    public $fulfillmentAssetId = '';
    public $fulfillmentComment = '';

    // Action State
    public $decisionComment = '';

    protected function queryString()
    {
        return [
            'view' => ['except' => 'inbox'],
            'search' => ['except' => ''],
            'status' => ['except' => 'all'],
            'type' => ['except' => 'all'],
        ];
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingView() { $this->resetPage(); }

    public function mount()
    {
        $this->templateSteps = [['role' => 'manager', 'label' => 'Manager Review']];
    }

    // --- Actions ---

    public function selectRequest($id)
    {
        $this->selectedRequestId = $id;
        $this->showDetailsModal = true;
        // Reset action state
        $this->decisionComment = '';
    }

    public function closeModals()
    {
        $this->showRequestModal = false;
        $this->showDetailsModal = false;
        $this->showTemplateModal = false;
        $this->showFulfillmentModal = false;
        $this->resetValidation();
    }

    public function openRequestModal($templateId = null)
    {
        $this->reset(['requestType', 'requestTemplateId', 'requestTitle', 'requestDescription', 'requestAmount', 'requestAttachment', 'requestDetails']);
        
        if ($templateId) {
            $template = WorkflowTemplate::find($templateId);
            if ($template) {
                $this->requestTemplateId = $template->id;
                $this->requestType = $template->type;
                $this->requestTitle = $template->default_title    ?: '';
                $this->requestDescription = $template->default_description ?: '';
            }
        }
        
        $this->showRequestModal = true;
    }

    public function submitRequest()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;
        $employee = Employee::where('email', $user->email)->where('tenant_id', $tenantId)->first();

        if (!$employee) {
            $this->addError('general', 'No employee profile linked to your user account.');
            return;
        }

        $this->validate([
            'requestTitle' => 'required|max:255',
            'requestType' => 'required|in:' . implode(',', array_keys(WorkflowRequest::types())),
            'requestAmount' => 'nullable|numeric',
            'requestAttachment' => 'nullable|file|max:10240',
        ]);

        $attachmentPayload = [];
        if ($this->requestAttachment) {
            $attachmentPayload = [
                'attachment_path' => $this->requestAttachment->store('workflow-attachments/' . $tenantId, 'local'),
                'attachment_name' => $this->requestAttachment->getClientOriginalName(),
                'attachment_size' => $this->requestAttachment->getSize(),
                'attachment_mime_type' => $this->requestAttachment->getMimeType(),
            ];
        }

        $workflowRequest = WorkflowRequest::create([
            'tenant_id' => $tenantId,
            'requester_user_id' => $user->id,
            'employee_id' => $employee->id,
            'workflow_template_id' => $this->requestTemplateId ?: null,
            'type' => $this->requestType,
            'title' => $this->requestTitle,
            'description' => $this->requestDescription,
            'amount' => $this->requestAmount ?: null,
            'details' => $this->requestDetails,
            'status' => 'pending',
            'submitted_at' => now(),
            ...$attachmentPayload,
        ]);

        $template = $this->requestTemplateId ? WorkflowTemplate::find($this->requestTemplateId) : null;
        $approverSteps = $template 
            ? $this->resolveApproverIdsFromTemplate($template, $employee, $user->id)
            : $this->resolveDefaultApproverIds($employee, $user->id);

        foreach ($approverSteps as $step) {
            WorkflowApproval::create([
                'workflow_request_id' => $workflowRequest->id,
                'tenant_id' => $tenantId,
                'approver_user_id' => $step['approver_user_id'],
                'comment' => $step['step_label'] ? '[Step] ' . $step['step_label'] : null,
            ]);
        }

        $this->notifyNextApprovers($workflowRequest);
        ActivityLogger::log('workflow.requested', $workflowRequest, ['title' => $workflowRequest->title]);

        $this->closeModals();
        $this->dispatch('notify', message: 'Mission request launched into approval grid.', type: 'success');
    }

    public function approveRequest()
    {
        $user = Auth::user();
        $request = WorkflowRequest::findOrFail($this->selectedRequestId);
        $approval = $request->approvals()->where('approver_user_id', $user->id)->where('decision', 'pending')->first();

        if (!$approval) return;

        $approval->update([
            'decision' => 'approved',
            'comment' => $this->decisionComment ?: $approval->comment,
            'acted_at' => now(),
        ]);

        if ($request->approvals()->where('decision', 'pending')->count() === 0) {
            $request->update(['status' => 'approved', 'resolved_at' => now(), 'last_action_by' => $user->id]);
            if ($request->requester) $request->requester->notify(new WorkflowApproved($request));
        } else {
            $request->update(['last_action_by' => $user->id]);
            $this->notifyNextApprovers($request);
        }

        ActivityLogger::log('workflow.approved', $request);
        $this->showDetailsModal = false;
        $this->dispatch('notify', message: 'Auth-signal approved.', type: 'success');
    }

    public function rejectRequest()
    {
        $this->validate(['decisionComment' => 'required']);
        $user = Auth::user();
        $request = WorkflowRequest::findOrFail($this->selectedRequestId);
        $approval = $request->approvals()->where('approver_user_id', $user->id)->where('decision', 'pending')->first();

        if (!$approval) return;

        $approval->update([
            'decision' => 'rejected',
            'comment' => $this->decisionComment,
            'acted_at' => now(),
        ]);

        $request->approvals()->where('decision', 'pending')->update(['decision' => 'cancelled', 'acted_at' => now()]);
        $request->update(['status' => 'rejected', 'resolved_at' => now(), 'last_action_by' => $user->id]);

        if ($request->requester) $request->requester->notify(new WorkflowRejected($request, $this->decisionComment));

        ActivityLogger::log('workflow.rejected', $request);
        $this->showDetailsModal = false;
        $this->dispatch('notify', message: 'Mission signal aborted.', type: 'warning');
    }

    public function fulfillAsset()
    {
        $this->validate(['fulfillmentAssetId' => 'required']);
        $user = Auth::user();
        $request = WorkflowRequest::findOrFail($this->selectedRequestId);
        
        $asset = Asset::findOrFail($this->fulfillmentAssetId);
        $asset->update([
            'employee_id' => $request->employee_id,
            'status' => 'assigned',
            'assigned_at' => now(),
            'notes' => trim(($asset->notes ? $asset->notes . PHP_EOL : '') . 'Assigned via workflow #' . $request->id),
        ]);

        $details = $request->details ?: [];
        $details['fulfilled_asset_id'] = $asset->id;
        $details['fulfilled_asset_name'] = $asset->name;
        $details['fulfilled_at'] = now()->toDateTimeString();

        $request->update(['status' => 'fulfilled', 'details' => $details, 'resolved_at' => now(), 'last_action_by' => $user->id]);
        if ($request->requester) $request->requester->notify(new WorkflowFulfilled($request, $asset->name));

        ActivityLogger::log('workflow.fulfilled', $request);
        $this->showFulfillmentModal = false;
        $this->showDetailsModal = false;
        $this->dispatch('notify', message: 'Asset deployed to recipient.', type: 'success');
    }

    public function downloadAttachment($id)
    {
        $doc = WorkflowRequest::findOrFail($id);
        return Storage::disk('local')->download($doc->attachment_path, $doc->attachment_name);
    }

    // --- Helpers ---

    private function resolveDefaultApproverIds(Employee $employee, $requesterId)
    {
        $steps = [];
        if ($employee->manager?->user) {
            $steps[] = ['approver_user_id' => $employee->manager->user->id, 'step_label' => 'Manager Review'];
        }
        
        $hrUsers = User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'hr_manager']))->get();
        foreach ($hrUsers as $u) {
            if ($u->id !== $requesterId) {
                $steps[] = ['approver_user_id' => $u->id, 'step_label' => 'HR Operations'];
            }
        }
        return collect($steps)->unique('approver_user_id')->toArray();
    }

    private function resolveApproverIdsFromTemplate(WorkflowTemplate $template, Employee $employee, $requesterId)
    {
        $steps = collect($template->approval_steps)->map(function($step) use ($employee, $requesterId) {
            if ($step['role'] === 'manager') {
                return $employee->manager?->user?->id !== $requesterId ? ['approver_user_id' => $employee->manager?->user?->id, 'step_label' => $step['label']] : null;
            }
            $userIds = User::whereHas('roles', fn($q) => $q->where('name', $step['role'] === 'admin' ? 'admin' : 'hr_manager'))->pluck('id');
            return $userIds->filter(fn($id) => $id !== $requesterId)->map(fn($id) => ['approver_user_id' => $id, 'step_label' => $step['label']])->toArray();
        })->flatten(1)->filter()->unique('approver_user_id')->toArray();

        return $steps ?: $this->resolveDefaultApproverIds($employee, $requesterId);
    }

    private function notifyNextApprovers($request)
    {
        $nextStepIds = $request->approvals()->where('decision', 'pending')->pluck('approver_user_id');
        $approvers = User::whereIn('id', $nextStepIds)->get();
        if ($approvers->isNotEmpty()) {
            Notification::send($approvers, new WorkflowSubmitted($request, $request->requester->name));
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = WorkflowRequest::query()->with(['requester', 'employee', 'template', 'approvals.approver'])->latest('submitted_at');

        if (!$user->hasAnyRole(['admin', 'hr_manager'])) {
            $query->where(function($q) use ($user) {
                $q->where('requester_user_id', $user->id)
                  ->orWhereHas('approvals', fn($aq) => $aq->where('approver_user_id', $user->id));
            });
        }

        if ($this->view === 'inbox') {
            $query->whereHas('approvals', fn($q) => $q->where('approver_user_id', $user->id)->where('decision', 'pending'));
        } elseif ($this->view === 'sent') {
            $query->where('requester_user_id', $user->id);
        }

        if ($this->status !== 'all') $query->where('status', $this->status);
        if ($this->type !== 'all') $query->where('type', $this->type);
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        $requests = $query->paginate(12);

        $templates = WorkflowTemplate::where('tenant_id', $user->tenant_id)->get();
        $availableAssets = Asset::where('tenant_id', $user->tenant_id)->where('status', 'available')->get();

        $summary = [
            'pending' => WorkflowRequest::where('status', 'pending')->count(),
            'approved' => WorkflowRequest::where('status', 'approved')->count(),
            'rejected' => WorkflowRequest::where('status', 'rejected')->count(),
            'inbox' => WorkflowRequest::whereHas('approvals', fn($q) => $q->where('approver_user_id', $user->id)->where('decision', 'pending'))->count(),
        ];

        return view('livewire.workflows.workflow-hub', [
            'requests' => $requests,
            'templates' => $templates,
            'summary' => $summary,
            'availableAssets' => $availableAssets,
            'selectedRequest' => $this->selectedRequestId ? WorkflowRequest::with(['approvals.approver', 'requester', 'employee'])->find($this->selectedRequestId) : null,
        ]);
    }
}
