<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'users',
            'departments',
            'employees',
            'leave_requests',
            'roles',
            'tenants',
            'documents',
            'shift_schedules',
            'employee_educations',
            'employee_experiences',
            'employee_skills',
            'holiday_policy_dates',
        ] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ([
            'holiday_policy_dates',
            'employee_skills',
            'employee_experiences',
            'employee_educations',
            'shift_schedules',
            'documents',
            'tenants',
            'roles',
            'leave_requests',
            'employees',
            'departments',
            'users',
        ] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropSoftDeletes();
                });
            }
        }
    }
};
