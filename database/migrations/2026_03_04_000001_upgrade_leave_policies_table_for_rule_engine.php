<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_policies', function (Blueprint $table) {
            if (! Schema::hasColumn('leave_policies', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id')->index();
            }
            if (! Schema::hasColumn('leave_policies', 'name')) {
                $table->string('name')->default('Default Leave Policy')->after('tenant_id');
            }
            if (! Schema::hasColumn('leave_policies', 'code')) {
                $table->string('code', 80)->nullable()->after('name');
            }
            if (! Schema::hasColumn('leave_policies', 'description')) {
                $table->text('description')->nullable()->after('code');
            }
            if (! Schema::hasColumn('leave_policies', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
            if (! Schema::hasColumn('leave_policies', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('leave_policies', 'effective_to')) {
                $table->date('effective_to')->nullable()->after('effective_from');
            }
            if (! Schema::hasColumn('leave_policies', 'carry_forward_limit')) {
                $table->unsignedInteger('carry_forward_limit')->default(0)->after('unpaid_limit');
            }
            if (! Schema::hasColumn('leave_policies', 'accrual_frequency')) {
                $table->string('accrual_frequency', 20)->default('monthly')->after('carry_forward_limit');
            }
            if (! Schema::hasColumn('leave_policies', 'rules')) {
                $table->json('rules')->nullable()->after('accrual_frequency');
            }
            if (! Schema::hasColumn('leave_policies', 'exceptions')) {
                $table->json('exceptions')->nullable()->after('rules');
            }
            if (! Schema::hasColumn('leave_policies', 'metadata')) {
                $table->json('metadata')->nullable()->after('exceptions');
            }
            if (! Schema::hasColumn('leave_policies', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('metadata')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('leave_policies', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('leave_policies', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('leave_policies', function (Blueprint $table) {
            $table->index(['tenant_id', 'is_active'], 'leave_policies_tenant_active_idx');
            $table->unique(['tenant_id', 'code'], 'leave_policies_tenant_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('leave_policies', function (Blueprint $table) {
            $table->dropUnique('leave_policies_tenant_code_unique');
            $table->dropIndex('leave_policies_tenant_active_idx');

            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('updated_by');
            $table->dropSoftDeletes();

            $table->dropColumn([
                'tenant_id',
                'name',
                'code',
                'description',
                'is_active',
                'effective_from',
                'effective_to',
                'carry_forward_limit',
                'accrual_frequency',
                'rules',
                'exceptions',
                'metadata',
            ]);
        });
    }
};
