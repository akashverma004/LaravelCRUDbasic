<?php

namespace Tests\Feature\Policies;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PolicyManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
    }

    public function test_admin_can_create_policy_for_each_module_via_api(): void
    {
        $this->authenticateAsAdminForApi();

        foreach ($this->modulePayloads() as $module => $payload) {
            $createResponse = $this->postJson("/api/policies/{$module}", $payload);

            $createResponse->assertCreated();
            $createResponse->assertJsonPath('data.name', $payload['name']);

            $policyId = (int) $createResponse->json('data.id');

            $this->getJson("/api/policies/{$module}")->assertOk();
            $this->getJson("/api/policies/{$module}/{$policyId}")->assertOk();
            $this->postJson("/api/policies/{$module}/{$policyId}/evaluate", [
                'context' => ['employee' => ['department' => 'engineering']],
            ])->assertOk();
            $this->postJson("/api/policies/{$module}/evaluate-active", [
                'context' => ['employee' => ['department' => 'engineering']],
            ])->assertOk();
        }
    }

    public function test_admin_can_update_and_delete_leave_policy_via_api(): void
    {
        $this->authenticateAsAdminForApi();

        $createResponse = $this->postJson('/api/policies/leave', [
            'name' => 'Leave Policy API Test',
            'code' => 'LEAVE_API_TEST',
            'annual_limit' => 15,
            'sick_limit' => 10,
            'casual_limit' => 7,
            'unpaid_limit' => 0,
            'carry_forward_limit' => 5,
            'accrual_frequency' => 'monthly',
            'rules' => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'employee.type', 'operator' => 'eq', 'value' => 'full-time'],
                ],
            ],
        ])->assertCreated();

        $policyId = (int) $createResponse->json('data.id');

        $this->patchJson("/api/policies/leave/{$policyId}", [
            'name' => 'Leave Policy Updated',
            'annual_limit' => 20,
        ])->assertOk()->assertJsonPath('data.name', 'Leave Policy Updated');

        $this->postJson("/api/policies/leave/{$policyId}/evaluate", [
            'context' => ['employee' => ['type' => 'full-time']],
        ])->assertOk()->assertJsonPath('data.passed', true);

        $this->deleteJson("/api/policies/leave/{$policyId}")
            ->assertOk()
            ->assertJsonPath('message', 'Policy deleted successfully.');
    }

    public function test_non_privileged_user_cannot_access_policy_api(): void
    {
        $tenantId = $this->getTenantId();
        $user = User::factory()->create([
            'tenant_id' => $tenantId,
            'is_platform_admin' => false,
            'password_changed_at' => now(),
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/policies/leave')->assertForbidden();
    }

    public function test_admin_can_view_and_update_policy_pages_with_reload_flow(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAs($admin)->withSession(['tenant_id' => $admin->tenant_id]);

        $this->get('/policies')->assertOk()->assertSee('Policies');

        $this->get('/policies/leave')->assertOk()->assertSee('Leave Policy');

        $this->patch('/policies/leave', [
            'name' => 'Leave Policy Web Update',
            'code' => 'LEAVE_WEB_DEFAULT',
            'annual_limit' => 18,
            'sick_limit' => 10,
            'casual_limit' => 7,
            'unpaid_limit' => 0,
            'carry_forward_limit' => 5,
            'accrual_frequency' => 'monthly',
            'is_active' => 1,
            'rules' => '{"mode":"all","conditions":[]}',
            'exceptions' => '{}',
            'metadata' => '{}',
        ])->assertRedirect('/policies/leave');

        $this->assertDatabaseHas('leave_policies', [
            'annual_limit' => 18,
            'sick_limit' => 10,
            'casual_limit' => 7,
            'unpaid_limit' => 0,
        ]);
    }

    public function test_non_privileged_user_cannot_access_policy_pages(): void
    {
        $tenantId = $this->getTenantId();
        $user = User::factory()->create([
            'tenant_id' => $tenantId,
            'is_platform_admin' => false,
            'password_changed_at' => now(),
        ]);
        $this->actingAs($user);

        $this->get('/policies')->assertForbidden();
    }

    private function authenticateAsAdminForApi(): User
    {
        $admin = $this->createUserWithRole('admin');
        Sanctum::actingAs($admin);

        return $admin;
    }

    private function getTenantId(): int
    {
        if (!\App\Models\Tenant::count()) {
            \App\Models\Tenant::factory()->create(['status' => 'active', 'onboarding_completed' => true]);
        }
        return \App\Models\Tenant::first()->id;
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate([
            'name' => $roleName,
        ], [
            'display_name' => ucfirst(str_replace('_', ' ', $roleName)),
            'description' => 'Test role',
        ]);

        $tenantId = $this->getTenantId();

        $user = User::factory()->create([
            'tenant_id' => $tenantId,
            'is_platform_admin' => false,
            'password_changed_at' => now(),
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function modulePayloads(): array
    {
        return [
            'leave' => [
                'name' => 'Leave Policy API',
                'code' => 'LEAVE_API',
                'annual_limit' => 15,
                'sick_limit' => 10,
                'casual_limit' => 7,
                'unpaid_limit' => 0,
                'carry_forward_limit' => 5,
                'accrual_frequency' => 'monthly',
            ],
            'attendance' => [
                'name' => 'Attendance Policy API',
                'code' => 'ATTENDANCE_API',
                'standard_hours_per_day' => 8,
                'grace_minutes' => 10,
                'max_late_marks_per_month' => 3,
                'work_days' => ['monday', 'tuesday'],
            ],
            'holiday' => [
                'name' => 'Holiday Policy API',
                'code' => 'HOLIDAY_API',
                'country_code' => 'IND',
                'state_code' => 'KA',
                'weekend_days' => ['saturday', 'sunday'],
            ],
            'payroll' => [
                'name' => 'Payroll Policy API',
                'code' => 'PAYROLL_API',
                'pay_cycle' => 'monthly',
                'pay_day' => 30,
                'cutoff_day' => 25,
                'prorate_on_join' => true,
                'prorate_on_exit' => true,
            ],
            'probation' => [
                'name' => 'Probation Policy API',
                'code' => 'PROBATION_API',
                'probation_days' => 90,
                'extension_allowed' => true,
                'max_extension_days' => 60,
            ],
            'notice-period' => [
                'name' => 'Notice Policy API',
                'code' => 'NOTICE_API',
                'notice_days' => 30,
                'buyout_allowed' => true,
                'waiver_allowed' => false,
            ],
            'overtime' => [
                'name' => 'Overtime Policy API',
                'code' => 'OVERTIME_API',
                'minimum_minutes' => 30,
                'weekday_multiplier' => 1.5,
                'weekend_multiplier' => 2.0,
                'holiday_multiplier' => 2.5,
                'max_hours_per_month' => 40,
            ],
            'wfh' => [
                'name' => 'WFH Policy API',
                'code' => 'WFH_API',
                'monthly_limit_days' => 8,
                'approval_required' => true,
                'max_consecutive_days' => 3,
                'allowed_departments' => ['engineering'],
                'allowed_roles' => ['employee'],
            ],
            'reimbursement' => [
                'name' => 'Reimbursement Policy API',
                'code' => 'REIMBURSEMENT_API',
                'monthly_claim_limit' => 25000,
                'single_claim_limit' => 10000,
                'receipt_required' => true,
                'allowed_categories' => ['travel', 'meal'],
                'approval_matrix' => ['manager', 'finance'],
            ],
            'code-of-conduct' => [
                'name' => 'Code Of Conduct API',
                'code' => 'COC_API',
                'document_version' => '1.0',
                'acknowledgement_required' => true,
                'policy_text' => 'Please follow conduct rules.',
                'breach_actions' => ['warning', 'suspension'],
            ],
        ];
    }
}
