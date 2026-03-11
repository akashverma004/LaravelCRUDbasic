<?php

namespace Tests\Feature\Workflows;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Asset;
use App\Models\ReimbursementPolicy;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\PayStructure;
use App\Models\WorkflowRequest;
use App\Models\WorkflowTemplate;
use App\Notifications\WorkflowApproved;
use App\Notifications\WorkflowFulfilled;
use App\Notifications\WorkflowRejected;
use App\Notifications\WorkflowSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkflowReimbursementFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_submit_reimbursement_with_attachment_and_approver_can_approve(): void
    {
        Storage::fake('local');
        Notification::fake();

        [$requester, $manager] = $this->seedWorkflowActors();

        $response = $this->actingAs($requester)->post(route('workflows.store'), [
            'type' => 'reimbursement',
            'title' => 'March travel reimbursement',
            'description' => 'Airport transfer and flight baggage.',
            'amount' => '2450.00',
            'details' => [
                'category' => 'travel',
                'expense_date' => now()->subDay()->toDateString(),
                'merchant' => 'Indigo Airlines',
                'receipt_reference' => 'INV-TRAVEL-1001',
                'notes' => 'Client visit expense',
            ],
            'attachment' => UploadedFile::fake()->create('receipt.pdf', 128, 'application/pdf'),
        ], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('request.type', 'reimbursement')
            ->assertJsonPath('request.has_attachment', true)
            ->assertJsonPath('request.details_preview', 'Travel - Indigo Airlines - ' . now()->subDay()->toDateString());

        $workflow = WorkflowRequest::firstOrFail();

        $this->assertSame('pending', $workflow->status);
        $this->assertNotNull($workflow->attachment_path);
        Storage::disk('local')->assertExists($workflow->attachment_path);

        Notification::assertSentTo($manager, WorkflowSubmitted::class, function (WorkflowSubmitted $notification) use ($manager, $workflow) {
            return $notification->toArray($manager)['action_url'] === '/workflows?workflow=' . $workflow->id . '&modal=timeline';
        });

        $approveResponse = $this->actingAs($manager)->post(route('workflows.approve', $workflow), [
            'comment' => 'Approved for reimbursement.',
        ], ['Accept' => 'application/json']);

        $approveResponse->assertOk()
            ->assertJsonPath('request.status', 'approved');

        $workflow->refresh();
        $this->assertSame('approved', $workflow->status);
        $this->assertNotNull($workflow->resolved_at);

        Notification::assertSentTo($requester, WorkflowApproved::class);
    }

    public function test_approver_can_reject_reimbursement_with_reason(): void
    {
        Storage::fake('local');
        Notification::fake();

        [$requester, $manager] = $this->seedWorkflowActors();

        $this->actingAs($requester)->post(route('workflows.store'), [
            'type' => 'reimbursement',
            'title' => 'Meal reimbursement',
            'description' => 'Team dinner after client workshop.',
            'amount' => '850.00',
            'details' => [
                'category' => 'meal',
                'expense_date' => now()->subDays(2)->toDateString(),
                'merchant' => 'Spice Terrace',
                'receipt_reference' => 'INV-MEAL-1002',
            ],
        ], ['Accept' => 'application/json'])->assertOk();

        $workflow = WorkflowRequest::firstOrFail();

        $rejectResponse = $this->actingAs($manager)->post(route('workflows.reject', $workflow), [
            'comment' => 'Please attach itemized bill next time.',
        ], ['Accept' => 'application/json']);

        $rejectResponse->assertOk()
            ->assertJsonPath('request.status', 'rejected');

        $workflow->refresh();
        $this->assertSame('rejected', $workflow->status);

        Notification::assertSentTo($requester, WorkflowRejected::class, function (WorkflowRejected $notification) use ($requester, $workflow) {
            return $notification->toArray($requester)['action_url'] === '/workflows?workflow=' . $workflow->id . '&action=resubmit';
        });
    }

    public function test_employee_can_submit_asset_request_and_manager_can_approve(): void
    {
        Notification::fake();

        [$requester, $manager] = $this->seedWorkflowActors();

        $response = $this->actingAs($requester)->post(route('workflows.store'), [
            'type' => 'asset-request',
            'title' => 'Need external monitor',
            'description' => 'Monitor required for dual-screen analysis work.',
            'details' => [
                'asset_category' => 'peripheral',
                'urgency' => 'medium',
                'needed_by' => now()->addWeek()->toDateString(),
                'preferred_model' => 'Dell 27 inch monitor',
                'business_reason' => 'Improves productivity for reporting and dashboard work.',
            ],
        ], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('request.type', 'asset-request')
            ->assertJsonPath('request.details_preview', 'Peripheral - Medium - ' . now()->addWeek()->toDateString());

        $workflow = WorkflowRequest::latest('id')->firstOrFail();

        $this->assertSame('asset-request', $workflow->type);
        $this->assertSame('peripheral', $workflow->details['asset_category']);

        Notification::assertSentTo($manager, WorkflowSubmitted::class);

        $this->actingAs($manager)->post(route('workflows.approve', $workflow), [
            'comment' => 'Approved, procure from available stock if possible.',
        ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('request.status', 'approved');

        Notification::assertSentTo($requester, WorkflowApproved::class);
    }

    public function test_admin_can_fulfill_approved_asset_request_with_available_inventory(): void
    {
        Notification::fake();

        [$requester, $manager] = $this->seedWorkflowActors();

        $this->actingAs($requester)->post(route('workflows.store'), [
            'type' => 'asset-request',
            'title' => 'Need laptop for new project',
            'description' => 'Portable device needed for client travel.',
            'details' => [
                'asset_category' => 'laptop',
                'urgency' => 'high',
                'needed_by' => now()->addDays(3)->toDateString(),
                'preferred_model' => 'Dell Latitude',
                'business_reason' => 'Required for onsite delivery work.',
            ],
        ], ['Accept' => 'application/json'])->assertOk();

        $workflow = WorkflowRequest::latest('id')->firstOrFail();

        $this->actingAs($manager)->post(route('workflows.approve', $workflow), [
            'comment' => 'Approved from inventory.',
        ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('request.status', 'approved');

        $admin = $this->createUserWithRole('admin', $requester->tenant_id);

        $asset = Asset::create([
            'tenant_id' => $requester->tenant_id,
            'employee_id' => null,
            'name' => 'Dell Latitude 7440',
            'serial_number' => 'DL-7440-001',
            'category' => 'laptop',
            'status' => 'available',
            'notes' => 'Fresh stock',
        ]);

        $response = $this->actingAs($admin)->post(route('workflows.fulfill-asset', $workflow), [
            'asset_id' => $asset->id,
            'comment' => 'Assigned from current IT inventory.',
        ], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('request.status', 'fulfilled')
            ->assertJsonPath('request.can_fulfill', false);

        $workflow->refresh();
        $asset->refresh();

        $requesterEmployee = Employee::where('email', $requester->email)->firstOrFail();

        $this->assertSame('fulfilled', $workflow->status);
        $this->assertSame($asset->id, $workflow->details['fulfilled_asset_id']);
        $this->assertSame($requesterEmployee->id, $asset->employee_id);
        $this->assertSame('assigned', $asset->status);
        $this->assertNotNull($asset->assigned_at);

        Notification::assertSentTo($requester, WorkflowFulfilled::class);

        $this->actingAs($requester)->get(route('workflows.show', $workflow), [
            'Accept' => 'application/json',
        ])->assertOk()
            ->assertJsonPath('request.status', 'fulfilled')
            ->assertJsonPath('request.fulfilled_asset.id', $asset->id)
            ->assertJsonPath('request.fulfilled_asset.name', 'Dell Latitude 7440')
            ->assertJsonPath('request.fulfilled_asset.serial_number', 'DL-7440-001')
            ->assertJsonPath('request.fulfilled_asset.assigned_to', $requesterEmployee->full_name)
            ->assertJsonPath('request.fulfilled_asset.fulfillment_note', 'Assigned from current IT inventory.');
    }

    public function test_admin_can_manage_templates_and_employee_can_submit_profile_change_with_template(): void
    {
        Notification::fake();

        [$requester, $manager] = $this->seedWorkflowActors();
        $hrManager = $this->createUserWithRole('hr_manager', $requester->tenant_id, 'hr@example.test');
        $admin = $this->createUserWithRole('admin', $requester->tenant_id, 'admin@example.test');

        $templateResponse = $this->actingAs($admin)->post(route('workflows.templates.store'), [
            'type' => 'profile-change',
            'name' => 'Profile Change Standard',
            'description' => 'Default profile update flow.',
            'default_title' => 'Profile Information Update',
            'default_description' => 'Please review my requested profile change.',
            'approval_steps' => [
                ['role' => 'manager', 'label' => 'Manager Check'],
                ['role' => 'hr_manager', 'label' => 'HR Verification'],
            ],
            'is_active' => true,
        ], ['Accept' => 'application/json']);

        $templateResponse->assertOk()
            ->assertJsonPath('template.type', 'profile-change')
            ->assertJsonPath('template.approval_summary.0', 'Manager Check - Manager');

        $templateId = $templateResponse->json('template.id');

        $submitResponse = $this->actingAs($requester)->post(route('workflows.store'), [
            'workflow_template_id' => $templateId,
            'type' => 'profile-change',
            'title' => 'Phone Number Update',
            'description' => 'Primary phone number needs correction.',
            'details' => [
                'field_name' => 'phone',
                'current_value' => '9999999002',
                'requested_value' => '9999999010',
                'effective_from' => now()->addDay()->toDateString(),
                'reason' => 'Current phone is being retired.',
            ],
        ], ['Accept' => 'application/json']);

        $submitResponse->assertOk()
            ->assertJsonPath('request.type', 'profile-change')
            ->assertJsonPath('request.template_name', 'Profile Change Standard')
            ->assertJsonPath('request.details_preview', 'Phone - 9999999010 - ' . now()->addDay()->toDateString());

        $workflow = WorkflowRequest::latest('id')->firstOrFail();
        $workflow->load('approvals');

        $this->assertSame($templateId, $workflow->workflow_template_id);
        $this->assertCount(2, $workflow->approvals);

        Notification::assertSentTo($manager, WorkflowSubmitted::class);
        Notification::assertNotSentTo($hrManager, WorkflowSubmitted::class);

        $showResponse = $this->actingAs($requester)->get(route('workflows.show', $workflow), [
            'Accept' => 'application/json',
        ]);

        $showResponse->assertOk()
            ->assertJsonPath('request.timeline.0.step_label', 'Manager Check')
            ->assertJsonPath('request.timeline.1.step_label', 'HR Verification');

        $this->actingAs($hrManager)->post(route('workflows.approve', $workflow), [
            'comment' => 'Trying early approval.',
        ], ['Accept' => 'application/json'])->assertForbidden();

        $this->actingAs($manager)->post(route('workflows.approve', $workflow), [
            'comment' => 'Manager approved.',
        ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('request.status', 'pending');

        Notification::assertSentTo($hrManager, WorkflowSubmitted::class);

        $this->actingAs($hrManager)->post(route('workflows.approve', $workflow), [
            'comment' => 'HR approved and applied.',
        ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('request.status', 'fulfilled');

        $workflow->refresh();
        $requesterEmployee = Employee::where('email', $requester->email)->firstOrFail();
        $requesterEmployee->refresh();

        $this->assertSame('fulfilled', $workflow->status);
        $this->assertSame('9999999010', $requesterEmployee->phone);
    }

    public function test_salary_change_updates_employee_and_pay_structure_after_final_approval(): void
    {
        Notification::fake();

        [$requester, $manager] = $this->seedWorkflowActors();
        $hrManager = $this->createUserWithRole('hr_manager', $requester->tenant_id, 'salary-hr@example.test');

        $requesterEmployee = Employee::where('email', $requester->email)->firstOrFail();

        PayStructure::create([
            'tenant_id' => $requester->tenant_id,
            'employee_id' => $requesterEmployee->id,
            'base_salary' => 60000,
            'allowances' => ['hra' => 5000],
            'deductions' => ['pf' => 1800],
            'currency' => 'INR',
        ]);

        $template = WorkflowTemplate::create([
            'tenant_id' => $requester->tenant_id,
            'type' => 'salary-change',
            'name' => 'Salary Revision',
            'approval_steps' => [
                ['role' => 'manager', 'label' => 'Manager Review'],
                ['role' => 'hr_manager', 'label' => 'Comp Review'],
            ],
            'is_active' => true,
        ]);

        $this->actingAs($requester)->post(route('workflows.store'), [
            'workflow_template_id' => $template->id,
            'type' => 'salary-change',
            'title' => 'Annual Salary Revision',
            'description' => 'Salary revision after performance cycle.',
            'details' => [
                'change_type' => 'raise',
                'requested_salary' => 72000,
                'effective_from' => now()->addDays(5)->toDateString(),
                'justification' => 'Approved in appraisal discussions.',
            ],
        ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('request.details_preview', 'Raise - INR 72,000.00 - ' . now()->addDays(5)->toDateString());

        $workflow = WorkflowRequest::latest('id')->firstOrFail();

        $this->actingAs($manager)->post(route('workflows.approve', $workflow), [
            'comment' => 'Manager approved.',
        ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('request.status', 'pending');

        $this->actingAs($hrManager)->post(route('workflows.approve', $workflow), [
            'comment' => 'Comp approved.',
        ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('request.status', 'fulfilled');

        $requesterEmployee->refresh();
        $workflow->refresh();
        $payStructure = PayStructure::where('employee_id', $requesterEmployee->id)->firstOrFail();

        $this->assertSame('72000.00', number_format((float) $requesterEmployee->salary, 2, '.', ''));
        $this->assertSame('72000.00', number_format((float) $payStructure->base_salary, 2, '.', ''));
        $this->assertSame('fulfilled', $workflow->status);
    }

    public function test_requester_can_cancel_pending_workflow_and_admin_can_archive_template(): void
    {
        [$requester, $manager] = $this->seedWorkflowActors();
        $admin = $this->createUserWithRole('admin', $requester->tenant_id, 'archive-admin@example.test');

        $template = WorkflowTemplate::create([
            'tenant_id' => $requester->tenant_id,
            'type' => 'general',
            'name' => 'General Approval',
            'approval_steps' => [
                ['role' => 'manager', 'label' => 'Manager Review'],
            ],
            'is_active' => true,
        ]);

        $this->actingAs($requester)->post(route('workflows.store'), [
            'type' => 'general',
            'title' => 'General workflow request',
            'description' => 'Need manual support.',
        ], ['Accept' => 'application/json'])->assertOk();

        $workflow = WorkflowRequest::latest('id')->firstOrFail();

        $this->actingAs($requester)->post(route('workflows.cancel', $workflow), [], [
            'Accept' => 'application/json',
        ])->assertOk()
            ->assertJsonPath('request.status', 'cancelled')
            ->assertJsonPath('request.can_cancel', false);

        $workflow->refresh();
        $this->assertSame('cancelled', $workflow->status);

        $this->actingAs($admin)->post(route('workflows.templates.archive', $template), [], [
            'Accept' => 'application/json',
        ])->assertOk()
            ->assertJsonPath('template.is_active', false);

        $template->refresh();
        $this->assertFalse($template->is_active);
    }

    public function test_requester_can_edit_and_resubmit_rejected_workflow(): void
    {
        Notification::fake();

        [$requester, $manager] = $this->seedWorkflowActors();

        $this->actingAs($requester)->post(route('workflows.store'), [
            'type' => 'profile-change',
            'title' => 'Address Update',
            'description' => 'Need to update city.',
            'details' => [
                'field_name' => 'city',
                'current_value' => 'Bengaluru',
                'requested_value' => 'Mysuru',
                'effective_from' => now()->addDay()->toDateString(),
                'reason' => 'Relocated recently.',
            ],
        ], ['Accept' => 'application/json'])->assertOk();

        $workflow = WorkflowRequest::latest('id')->firstOrFail();

        $this->actingAs($manager)->post(route('workflows.reject', $workflow), [
            'comment' => 'Please correct the city name.',
        ], ['Accept' => 'application/json'])->assertOk();

        $this->actingAs($requester)->get(route('workflows.show', $workflow), [
            'Accept' => 'application/json',
        ])->assertOk()
            ->assertJsonPath('request.can_resubmit', true);

        $this->actingAs($requester)->post(route('workflows.resubmit', $workflow), [
            'type' => 'profile-change',
            'title' => 'Address Update',
            'description' => 'Need to update city.',
            'details' => [
                'field_name' => 'city',
                'current_value' => 'Bengaluru',
                'requested_value' => 'Mysore',
                'effective_from' => now()->addDay()->toDateString(),
                'reason' => 'Relocated recently.',
            ],
        ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('request.status', 'pending')
            ->assertJsonPath('request.can_resubmit', false)
            ->assertJsonPath('request.details_preview', 'City - Mysore - ' . now()->addDay()->toDateString());

        $workflow->refresh();
        $this->assertSame('pending', $workflow->status);
        $this->assertSame('Mysore', $workflow->details['requested_value']);
        $this->assertCount(1, $workflow->approvals);
    }

    private function seedWorkflowActors(): array
    {
        $tenant = Tenant::create([
            'name' => 'Workflow Tenant',
            'code' => 'WFLOW',
            'slug' => 'workflow-tenant',
            'email' => 'ops@example.test',
            'is_active' => true,
            'setup_completed' => true,
        ]);

        ReimbursementPolicy::create([
            'tenant_id' => $tenant->id,
            'name' => 'Default Reimbursement',
            'code' => 'REIMB_DEFAULT',
            'is_active' => true,
            'effective_from' => now()->subDay()->toDateString(),
            'single_claim_limit' => 5000,
            'receipt_required' => true,
            'allowed_categories' => ['travel', 'meal'],
        ]);

        $department = Department::create([
            'tenant_id' => $tenant->id,
            'name' => 'Operations',
            'code' => 'OPS',
            'lead_name' => 'Workflow Lead',
        ]);

        $manager = User::factory()->create([
            'tenant_id' => $tenant->id,
            'email' => 'manager@example.test',
            'require_password_change' => false,
        ]);

        $managerEmployee = Employee::create([
            'tenant_id' => $tenant->id,
            'department_id' => $department->id,
            'manager_id' => null,
            'role_id' => null,
            'full_name' => 'Manager User',
            'email' => $manager->email,
            'phone' => '9999999001',
            'job_title' => 'Manager',
            'employment_type' => 'full-time',
            'salary' => 80000,
            'joined_on' => now()->subYear()->toDateString(),
            'status' => 'active',
            'country' => 'IN',
            'state' => 'KA',
            'city' => 'Bengaluru',
            'address' => 'Manager Address',
        ]);

        $requester = User::factory()->create([
            'tenant_id' => $tenant->id,
            'email' => 'employee@example.test',
            'require_password_change' => false,
        ]);

        Employee::create([
            'tenant_id' => $tenant->id,
            'department_id' => $department->id,
            'manager_id' => $managerEmployee->id,
            'role_id' => null,
            'full_name' => 'Requester User',
            'email' => $requester->email,
            'phone' => '9999999002',
            'job_title' => 'Engineer',
            'employment_type' => 'full-time',
            'salary' => 60000,
            'joined_on' => now()->subMonths(6)->toDateString(),
            'status' => 'active',
            'country' => 'IN',
            'state' => 'KA',
            'city' => 'Bengaluru',
            'address' => 'Requester Address',
        ]);

        return [$requester, $manager];
    }

    private function createUserWithRole(string $roleName, int $tenantId, ?string $email = null): User
    {
        $role = Role::query()->firstOrCreate([
            'tenant_id' => $tenantId,
            'name' => $roleName,
        ], [
            'display_name' => ucfirst(str_replace('_', ' ', $roleName)),
            'description' => 'Workflow test role',
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenantId,
            'email' => $email ?: strtolower($roleName) . '+' . fake()->unique()->numerify('###') . '@example.test',
            'require_password_change' => false,
        ]);

        $user->assignRole($role);

        return $user;
    }
}
