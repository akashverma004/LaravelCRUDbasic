<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Support\TenantContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrmsSeeder extends Seeder
{
    public function run(): void
    {
        $tenantIds = DB::table('tenants')->pluck('id')->all();
        if (empty($tenantIds)) {
            $tenantIds = [1];
        }

        foreach ($tenantIds as $tenantId) {
            TenantContext::set((int) $tenantId);

            $departments = collect([
                ['name' => 'Engineering', 'code' => 'ENG', 'lead_name' => 'Anika Sharma'],
                ['name' => 'People Ops', 'code' => 'HRO', 'lead_name' => 'Rohan Mehta'],
                ['name' => 'Finance', 'code' => 'FIN', 'lead_name' => 'Megha Patel'],
                ['name' => 'Design', 'code' => 'DSN', 'lead_name' => 'Karan Jain'],
            ])->map(fn (array $department) => Department::create($department));

            $employees = collect([
                ['full_name' => 'Ava Thomas', 'email' => 'ava@peopleflow.test', 'phone' => '+91-987650001', 'job_title' => 'Frontend Engineer', 'employment_type' => 'full-time', 'salary' => 1200000, 'joined_on' => now()->subMonths(16), 'status' => 'active', 'department_id' => $departments[0]->id],
                ['full_name' => 'Liam Joseph', 'email' => 'liam@peopleflow.test', 'phone' => '+91-987650002', 'job_title' => 'HR Specialist', 'employment_type' => 'full-time', 'salary' => 820000, 'joined_on' => now()->subMonths(8), 'status' => 'active', 'department_id' => $departments[1]->id],
                ['full_name' => 'Mia Kapoor', 'email' => 'mia@peopleflow.test', 'phone' => '+91-987650003', 'job_title' => 'Account Analyst', 'employment_type' => 'contract', 'salary' => 700000, 'joined_on' => now()->subMonths(5), 'status' => 'on-leave', 'department_id' => $departments[2]->id],
                ['full_name' => 'Noah Arora', 'email' => 'noah@peopleflow.test', 'phone' => '+91-987650004', 'job_title' => 'Product Designer', 'employment_type' => 'full-time', 'salary' => 980000, 'joined_on' => now()->subMonths(10), 'status' => 'active', 'department_id' => $departments[3]->id],
            ])->map(fn (array $employee) => Employee::create($employee));

            foreach ([
                ['employee_id' => $employees[2]->id, 'leave_type' => 'sick', 'start_date' => now()->subDays(2), 'end_date' => now()->addDays(2), 'reason' => 'Medical recovery', 'status' => 'approved'],
                ['employee_id' => $employees[1]->id, 'leave_type' => 'annual', 'start_date' => now()->addDays(7), 'end_date' => now()->addDays(10), 'reason' => 'Family travel', 'status' => 'pending'],
            ] as $leaveData) {
                LeaveRequest::create($leaveData);
            }

            foreach ($employees as $employee) {
                AttendanceRecord::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => now()->toDateString(),
                    'clock_in_at' => '09:30:00',
                    'clock_out_at' => null,
                    'work_mode' => 'hybrid',
                ]);
            }
        }

        TenantContext::set(null);
    }
}
