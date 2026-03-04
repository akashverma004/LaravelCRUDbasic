<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tenants')) {
            Schema::create('tenants', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 80)->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        DB::table('tenants')->updateOrInsert(
            ['code' => 'DEFAULT'],
            [
                'name' => 'Default Tenant',
                'is_active' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $defaultTenantId = (int) DB::table('tenants')->where('code', 'DEFAULT')->value('id');

        $this->addTenantColumn('users', $defaultTenantId);
        $this->addTenantColumn('departments', $defaultTenantId);
        $this->addTenantColumn('employees', $defaultTenantId);
        $this->addTenantColumn('leave_requests', $defaultTenantId);
        $this->addTenantColumn('attendance_records', $defaultTenantId);
        $this->addTenantColumn('roles', $defaultTenantId);
        $this->addTenantColumn('permissions', $defaultTenantId);
        $this->addTenantColumn('role_permission', $defaultTenantId);
        $this->addTenantColumn('user_role', $defaultTenantId);
        $this->addTenantColumn('employee_leave_policies', $defaultTenantId);
        $this->addTenantColumn('holiday_policy_dates', $defaultTenantId);

        foreach ([
            'leave_policies',
            'attendance_policies',
            'holiday_policies',
            'payroll_policies',
            'probation_policies',
            'notice_period_policies',
            'overtime_policies',
            'wfh_policies',
            'reimbursement_policies',
            'code_of_conduct_policies',
        ] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
            }
        }

        // Unique constraints hardened for multi-tenant naming semantics.
        $this->dropUniqueIfExists('users', 'users_email_unique');
        $this->addUniqueIfMissing('users', ['tenant_id', 'email'], 'users_tenant_email_unique');

        $this->dropUniqueIfExists('departments', 'departments_code_unique');
        $this->addUniqueIfMissing('departments', ['tenant_id', 'code'], 'departments_tenant_code_unique');

        $this->dropUniqueIfExists('employees', 'employees_email_unique');
        $this->addUniqueIfMissing('employees', ['tenant_id', 'email'], 'employees_tenant_email_unique');

        // Keep old attendance unique (backed by FK index), add tenant-aware unique for forward compatibility.
        $this->addUniqueIfMissing(
            'attendance_records',
            ['tenant_id', 'employee_id', 'attendance_date'],
            'attendance_records_tenant_employee_date_unique'
        );

        $this->dropUniqueIfExists('roles', 'roles_name_unique');
        $this->addUniqueIfMissing('roles', ['tenant_id', 'name'], 'roles_tenant_name_unique');

        $this->dropUniqueIfExists('permissions', 'permissions_name_unique');
        $this->addUniqueIfMissing('permissions', ['tenant_id', 'name'], 'permissions_tenant_name_unique');

        // Additional tenant-side helper indexes.
        $this->addIndexIfMissing('leave_requests', ['tenant_id', 'status'], 'leave_requests_tenant_status_idx');
        $this->addIndexIfMissing('holiday_policy_dates', ['tenant_id', 'holiday_date'], 'holiday_policy_dates_tenant_date_idx');
    }

    public function down(): void
    {
        // Intentionally no-op for safety in shared environments.
    }

    private function addTenantColumn(string $table, int $defaultTenantId): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, 'tenant_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($defaultTenantId) {
            $tableBlueprint->unsignedBigInteger('tenant_id')->default($defaultTenantId)->after('id')->index();
        });

        DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
    }

    private function dropUniqueIfExists(string $table, string $indexName): void
    {
        if (! $this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
            $blueprint->dropUnique($indexName);
        });
    }

    private function addUniqueIfMissing(string $table, array $columns, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
            $blueprint->unique($columns, $indexName);
        });
    }

    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
            $blueprint->index($columns, $indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
