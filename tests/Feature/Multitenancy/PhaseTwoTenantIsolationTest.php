<?php

namespace Tests\Feature\Multitenancy;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PhaseTwoTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_user_model_rejects_cross_tenant_role_assignment(): void
    {
        $tenantA = 1;
        $tenantB = $this->createTenant('T2');

        $user = User::factory()->create(['tenant_id' => $tenantA]);
        $foreignRole = Role::query()->create([
            'tenant_id' => $tenantB,
            'name' => 'admin_t2',
            'display_name' => 'Admin T2',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $user->assignRole($foreignRole);
    }

    public function test_role_assignment_endpoint_blocks_foreign_tenant_role_ids(): void
    {
        $tenantA = 1;
        $tenantB = $this->createTenant('T3');

        $admin = $this->createAdminWithManageRolesPermission($tenantA);
        $targetUser = User::factory()->create(['tenant_id' => $tenantA]);
        $foreignRole = Role::query()->create([
            'tenant_id' => $tenantB,
            'name' => 'finance_t3',
            'display_name' => 'Finance T3',
        ]);

        $this->actingAs($admin)
            ->post(route('users.assign-role', $targetUser), ['role_id' => $foreignRole->id])
            ->assertSessionHasErrors('role_id');
    }

    public function test_policy_api_ignores_payload_tenant_id_and_uses_context_tenant(): void
    {
        $tenantA = 1;
        $tenantB = $this->createTenant('T4');
        $admin = $this->createAdminWithManageRolesPermission($tenantA);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/policies/leave', [
            'tenant_id' => $tenantB,
            'name' => 'Tenant A Leave Policy',
            'code' => 'LEAVE_TENANT_A',
            'annual_limit' => 12,
            'sick_limit' => 8,
            'casual_limit' => 6,
            'unpaid_limit' => 0,
            'carry_forward_limit' => 5,
            'accrual_frequency' => 'monthly',
        ])->assertCreated();

        $policyId = (int) $response->json('data.id');
        $this->assertDatabaseHas('leave_policies', [
            'id' => $policyId,
            'tenant_id' => $tenantA,
            'code' => 'LEAVE_TENANT_A',
        ]);
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

    private function createAdminWithManageRolesPermission(int $tenantId): User
    {
        $permission = Permission::query()->firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'role.manage'],
            ['display_name' => 'Manage Roles', 'module' => 'settings']
        );

        $role = Role::query()->firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Tenant admin']
        );
        $role->givePermission($permission);

        $user = User::factory()->create(['tenant_id' => $tenantId]);
        $user->assignRole($role);

        return $user;
    }
}
