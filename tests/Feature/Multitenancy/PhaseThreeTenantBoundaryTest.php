<?php

namespace Tests\Feature\Multitenancy;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmployeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PhaseThreeTenantBoundaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_employee_create_rejects_department_from_other_tenant(): void
    {
        $tenantA = 1;
        $tenantB = $this->createTenant('T5');

        $user = User::factory()->create(['tenant_id' => $tenantA]);
        $foreignDepartment = Department::query()->create([
            'tenant_id' => $tenantB,
            'name' => 'Foreign Dept',
            'code' => 'FDPT',
            'lead_name' => 'Lead',
        ]);

        $this->actingAs($user)
            ->post(route('employees.store'), $this->employeePayload([
                'department_id' => $foreignDepartment->id,
                'email' => 'cross-tenant-dept@test.local',
            ]))
            ->assertSessionHasErrors('department_id');
    }

    public function test_leave_create_rejects_employee_from_other_tenant(): void
    {
        $tenantA = 1;
        $tenantB = $this->createTenant('T6');

        $user = User::factory()->create(['tenant_id' => $tenantA]);
        $foreignDepartment = Department::query()->create([
            'tenant_id' => $tenantB,
            'name' => 'Foreign Leave Dept',
            'code' => 'FLDP',
            'lead_name' => 'Lead',
        ]);
        $foreignEmployee = Employee::query()->create([
            'tenant_id' => $tenantB,
            'department_id' => $foreignDepartment->id,
            'full_name' => 'Foreign Employee',
            'email' => 'foreign.employee@test.local',
            'phone' => '9999999999',
            'job_title' => 'Engineer',
            'employment_type' => 'full-time',
            'salary' => 50000,
            'joined_on' => now()->subMonth()->toDateString(),
            'status' => 'active',
            'country' => 'IN',
            'state' => 'KA',
            'city' => 'Bengaluru',
            'address' => 'Address',
        ]);

        $this->actingAs($user)
            ->post(route('leaves.store'), [
                'employee_id' => $foreignEmployee->id,
                'leave_type' => 'annual',
                'leave_session' => 'full_day',
                'start_date' => now()->addDay()->toDateString(),
                'end_date' => now()->addDay()->toDateString(),
                'reason' => 'Validation check',
                'status' => 'pending',
            ])
            ->assertSessionHasErrors('employee_id');
    }

    public function test_employee_search_does_not_leak_other_tenant_records(): void
    {
        $tenantA = 1;
        $tenantB = $this->createTenant('T7');

        $user = User::factory()->create(['tenant_id' => $tenantA]);
        $departmentA = Department::query()->create([
            'tenant_id' => $tenantA,
            'name' => 'Dept A',
            'code' => 'DPA',
            'lead_name' => 'Lead A',
        ]);
        $departmentB = Department::query()->create([
            'tenant_id' => $tenantB,
            'name' => 'Dept B',
            'code' => 'DPB',
            'lead_name' => 'Lead B',
        ]);

        Employee::query()->create([
            'tenant_id' => $tenantA,
            'department_id' => $departmentA->id,
            'full_name' => 'Visible Employee',
            'email' => 'shared-search@test.local',
            'phone' => '1111111111',
            'job_title' => 'Analyst',
            'employment_type' => 'full-time',
            'salary' => 45000,
            'joined_on' => now()->subMonth()->toDateString(),
            'status' => 'active',
            'country' => 'IN',
            'state' => 'KA',
            'city' => 'Bengaluru',
            'address' => 'Address A',
        ]);
        Employee::query()->create([
            'tenant_id' => $tenantB,
            'department_id' => $departmentB->id,
            'full_name' => 'Hidden Employee',
            'email' => 'shared-search@test.local',
            'phone' => '2222222222',
            'job_title' => 'Analyst',
            'employment_type' => 'full-time',
            'salary' => 47000,
            'joined_on' => now()->subMonth()->toDateString(),
            'status' => 'active',
            'country' => 'IN',
            'state' => 'KA',
            'city' => 'Bengaluru',
            'address' => 'Address B',
        ]);

        $this->actingAs($user);
        $results = app(EmployeeService::class)->searchEmployees('shared-search@test.local');

        $this->assertCount(1, $results->items());
        $this->assertSame('Visible Employee', $results->items()[0]->full_name);
    }

    private function createTenant(string $code): int
    {
        return (int) DB::table('tenants')->insertGetId([
            'name' => "Tenant {$code}",
            'code' => $code,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function employeePayload(array $overrides = []): array
    {
        return array_merge([
            'department_id' => 1,
            'manager_id' => null,
            'role_id' => null,
            'full_name' => 'Test Employee',
            'email' => 'employee.test@example.com',
            'phone' => '9998887777',
            'job_title' => 'Engineer',
            'employment_type' => 'full-time',
            'salary' => 65000,
            'joined_on' => now()->subDay()->toDateString(),
            'status' => 'active',
            'country' => 'IN',
            'state' => 'KA',
            'city' => 'Bengaluru',
            'address' => 'Test Address',
            'hobbies' => null,
            'likes' => null,
            'food_preference' => null,
            'health_issues' => null,
        ], $overrides);
    }
}
