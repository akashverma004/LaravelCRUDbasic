<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Support\TenantContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $tenantIds = DB::table('tenants')->pluck('id')->all();
        if (empty($tenantIds)) {
            $tenantIds = [1];
        }

        foreach ($tenantIds as $tenantId) {
            TenantContext::set((int) $tenantId);
            $this->seedForCurrentTenant();
        }

        TenantContext::set(null);
        $this->command?->info('Organization hierarchy seeded for all tenants.');
    }

    private function seedForCurrentTenant(): void
    {
        $departments = [
            'ENG' => Department::query()->firstOrCreate(['code' => 'ENG'], ['name' => 'Engineering', 'lead_name' => 'John Smith']),
            'SALES' => Department::query()->firstOrCreate(['code' => 'SALES'], ['name' => 'Sales', 'lead_name' => 'Sarah Johnson']),
            'HR' => Department::query()->firstOrCreate(['code' => 'HR'], ['name' => 'Human Resources', 'lead_name' => 'Mike Wilson']),
            'MARKET' => Department::query()->firstOrCreate(['code' => 'MARKET'], ['name' => 'Marketing', 'lead_name' => 'Lisa Brown']),
        ];

        $ceo = $this->upsertEmployee('ceo@company.com', [
            'full_name' => 'Rajesh Kumar',
            'department_id' => $departments['ENG']->id,
            'manager_id' => null,
            'phone' => '+91-98765-43210',
            'job_title' => 'Chief Executive Officer',
            'employment_type' => 'full-time',
            'salary' => 150000,
            'joined_on' => now()->subYears(5),
            'status' => 'active',
        ]);

        Employee::query()->whereNull('manager_id')->where('id', '!=', $ceo->id)->update(['manager_id' => $ceo->id]);

        $engHead = $this->upsertEmployee('john.smith@company.com', [
            'full_name' => 'John Smith',
            'department_id' => $departments['ENG']->id,
            'manager_id' => $ceo->id,
            'phone' => '+91-98765-43211',
            'job_title' => 'VP Engineering',
            'employment_type' => 'full-time',
            'salary' => 120000,
            'joined_on' => now()->subYears(4),
            'status' => 'active',
        ]);

        $salesHead = $this->upsertEmployee('sarah.johnson@company.com', [
            'full_name' => 'Sarah Johnson',
            'department_id' => $departments['SALES']->id,
            'manager_id' => $ceo->id,
            'phone' => '+91-98765-43212',
            'job_title' => 'VP Sales',
            'employment_type' => 'full-time',
            'salary' => 110000,
            'joined_on' => now()->subYears(3),
            'status' => 'active',
        ]);

        $hrHead = $this->upsertEmployee('mike.wilson@company.com', [
            'full_name' => 'Mike Wilson',
            'department_id' => $departments['HR']->id,
            'manager_id' => $ceo->id,
            'phone' => '+91-98765-43213',
            'job_title' => 'VP Human Resources',
            'employment_type' => 'full-time',
            'salary' => 100000,
            'joined_on' => now()->subYears(3),
            'status' => 'active',
        ]);

        $marketingHead = $this->upsertEmployee('lisa.brown@company.com', [
            'full_name' => 'Lisa Brown',
            'department_id' => $departments['MARKET']->id,
            'manager_id' => $ceo->id,
            'phone' => '+91-98765-43214',
            'job_title' => 'VP Marketing',
            'employment_type' => 'full-time',
            'salary' => 95000,
            'joined_on' => now()->subYears(2),
            'status' => 'active',
        ]);

        $employees = [
            ['email' => 'amit.patel@company.com', 'full_name' => 'Amit Patel', 'department_id' => $departments['ENG']->id, 'manager_id' => $engHead->id, 'phone' => '+91-98765-43215', 'job_title' => 'Senior PHP Developer', 'employment_type' => 'full-time', 'salary' => 75000, 'joined_on' => now()->subYears(2), 'status' => 'active'],
            ['email' => 'priya.sharma@company.com', 'full_name' => 'Priya Sharma', 'department_id' => $departments['ENG']->id, 'manager_id' => $engHead->id, 'phone' => '+91-98765-43216', 'job_title' => 'Frontend Developer', 'employment_type' => 'full-time', 'salary' => 65000, 'joined_on' => now()->subYears(1), 'status' => 'active'],
            ['email' => 'rajesh.kumar@company.com', 'full_name' => 'Rajesh Kumar', 'department_id' => $departments['ENG']->id, 'manager_id' => $engHead->id, 'phone' => '+91-98765-43217', 'job_title' => 'DevOps Engineer', 'employment_type' => 'full-time', 'salary' => 70000, 'joined_on' => now()->subMonths(8), 'status' => 'active'],
            ['email' => 'arjun.nair@company.com', 'full_name' => 'Arjun Nair', 'department_id' => $departments['SALES']->id, 'manager_id' => $salesHead->id, 'phone' => '+91-98765-43218', 'job_title' => 'Sales Manager - Enterprise', 'employment_type' => 'full-time', 'salary' => 80000, 'joined_on' => now()->subYears(2), 'status' => 'active'],
            ['email' => 'neha.reddy@company.com', 'full_name' => 'Neha Reddy', 'department_id' => $departments['SALES']->id, 'manager_id' => $salesHead->id, 'phone' => '+91-98765-43219', 'job_title' => 'Sales Executive', 'employment_type' => 'full-time', 'salary' => 55000, 'joined_on' => now()->subMonths(6), 'status' => 'active'],
            ['email' => 'vikram.singh@company.com', 'full_name' => 'Vikram Singh', 'department_id' => $departments['SALES']->id, 'manager_id' => $salesHead->id, 'phone' => '+91-98765-43220', 'job_title' => 'Account Executive', 'employment_type' => 'full-time', 'salary' => 50000, 'joined_on' => now()->subMonths(4), 'status' => 'active'],
            ['email' => 'kavya.gupta@company.com', 'full_name' => 'Kavya Gupta', 'department_id' => $departments['HR']->id, 'manager_id' => $hrHead->id, 'phone' => '+91-98765-43221', 'job_title' => 'HR Manager', 'employment_type' => 'full-time', 'salary' => 65000, 'joined_on' => now()->subYears(1), 'status' => 'active'],
            ['email' => 'ananya.sen@company.com', 'full_name' => 'Ananya Sen', 'department_id' => $departments['HR']->id, 'manager_id' => $hrHead->id, 'phone' => '+91-98765-43222', 'job_title' => 'Recruiter', 'employment_type' => 'full-time', 'salary' => 45000, 'joined_on' => now()->subMonths(3), 'status' => 'active'],
            ['email' => 'ashok.desai@company.com', 'full_name' => 'Ashok Desai', 'department_id' => $departments['MARKET']->id, 'manager_id' => $marketingHead->id, 'phone' => '+91-98765-43223', 'job_title' => 'Digital Marketing Manager', 'employment_type' => 'full-time', 'salary' => 60000, 'joined_on' => now()->subYears(1), 'status' => 'active'],
            ['email' => 'sneha.iyer@company.com', 'full_name' => 'Sneha Iyer', 'department_id' => $departments['MARKET']->id, 'manager_id' => $marketingHead->id, 'phone' => '+91-98765-43224', 'job_title' => 'Content Strategist', 'employment_type' => 'full-time', 'salary' => 55000, 'joined_on' => now()->subMonths(10), 'status' => 'active'],
        ];

        foreach ($employees as $employee) {
            $email = $employee['email'];
            unset($employee['email']);
            $this->upsertEmployee($email, $employee);
        }
    }

    private function upsertEmployee(string $email, array $attributes): Employee
    {
        return Employee::query()->firstOrCreate(['email' => $email], $attributes);
    }
}
