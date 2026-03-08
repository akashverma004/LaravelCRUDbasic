<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add composite (tenant_id, code) unique indexes on all policy tables so that:
     * 1. Two tenants can use the same code slug (e.g. both have 'LEAVE_DEFAULT').
     * 2. A single tenant cannot have two policies with the same code in one table.
     * 3. The BelongsToTenant global scope is backed by a DB-level guarantee.
     */
    public function up(): void
    {
        $tables = [
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
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            // Drop old global unique on code if it exists
            $this->dropUniqueIfExists($table, $table . '_code_unique');

            // Add the tenant-scoped composite unique
            $indexName = $table . '_tenant_code_unique';
            if (! $this->indexExists($table, $indexName)) {
                // First ensure there are no duplicate (tenant_id, code) rows
                // (data fix: append the row ID to any duplicate codes)
                DB::statement("
                    UPDATE {$table} t1
                    JOIN (
                        SELECT id, tenant_id, code,
                               ROW_NUMBER() OVER (PARTITION BY tenant_id, code ORDER BY id) AS rn
                        FROM {$table}
                    ) ranked ON t1.id = ranked.id
                    SET t1.code = CONCAT(t1.code, '_', t1.id)
                    WHERE ranked.rn > 1
                ");

                Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                    $blueprint->unique(['tenant_id', 'code'], $indexName);
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'leave_policies', 'attendance_policies', 'holiday_policies',
            'payroll_policies', 'probation_policies', 'notice_period_policies',
            'overtime_policies', 'wfh_policies', 'reimbursement_policies',
            'code_of_conduct_policies',
        ];

        foreach ($tables as $table) {
            $indexName = $table . '_tenant_code_unique';
            $this->dropUniqueIfExists($table, $indexName);
        }
    }

    private function dropUniqueIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, fn (Blueprint $b) => $b->dropUnique($indexName));
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
