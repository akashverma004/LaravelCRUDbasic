<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->index(['tenant_id', 'department_id', 'status'], 'idx_emp_tenant_dept_status');
            $table->index(['tenant_id', 'employment_type'], 'idx_emp_tenant_type');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->index(['tenant_id', 'employee_id', 'attendance_date'], 'idx_att_tenant_emp_date');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->index(['tenant_id', 'employee_id', 'status'], 'idx_leave_tenant_emp_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_emp_tenant_dept_status');
            $table->dropIndex('idx_emp_tenant_type');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex('idx_att_tenant_emp_date');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropIndex('idx_leave_tenant_emp_status');
        });
    }
};
