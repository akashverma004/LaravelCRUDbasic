<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Database\Seeder;

class OrganizationHierarchySeeder extends Seeder
{
    public function run(): void
    {
        // Get or create departments
        $engineeringDept = Department::firstOrCreate(['code' => 'ENG'], [
            'name' => 'Engineering',
            'lead_name' => 'John Smith',
        ]);

        $salesDept = Department::firstOrCreate(['code' => 'SALES'], [
            'name' => 'Sales',
            'lead_name' => 'Sarah Johnson',
        ]);

        $hrDept = Department::firstOrCreate(['code' => 'HR'], [
            'name' => 'Human Resources',
            'lead_name' => 'Mike Wilson',
        ]);

        $marketingDept = Department::firstOrCreate(['code' => 'MARKET'], [
            'name' => 'Marketing',
            'lead_name' => 'Lisa Brown',
        ]);

        // CEO - No manager
        $ceo = Employee::firstOrCreate(['email' => 'ceo@company.com'], [
            'full_name' => 'Rajesh Kumar',
            'department_id' => $engineeringDept->id,
            'manager_id' => null,
            'phone' => '+91-98765-43210',
            'job_title' => 'Chief Executive Officer',
            'employment_type' => 'full-time',
            'salary' => 150000,
            'joined_on' => now()->subYears(5),
            'status' => 'active',
        ]);

        // Link any orphaned employees (without managers and not the CEO) to the CEO
        Employee::whereNull('manager_id')
            ->where('id', '!=', $ceo->id)
            ->update(['manager_id' => $ceo->id]);

        // Engineering Head - Reports to CEO
        $engHead = Employee::firstOrCreate(['email' => 'john.smith@company.com'], [
            'full_name' => 'John Smith',
            'department_id' => $engineeringDept->id,
            'manager_id' => $ceo->id,
            'phone' => '+91-98765-43211',
            'job_title' => 'VP Engineering',
            'employment_type' => 'full-time',
            'salary' => 120000,
            'joined_on' => now()->subYears(4),
            'status' => 'active',
        ]);

        // Sales Head - Reports to CEO
        $salesHead = Employee::firstOrCreate(['email' => 'sarah.johnson@company.com'], [
            'full_name' => 'Sarah Johnson',
            'department_id' => $salesDept->id,
            'manager_id' => $ceo->id,
            'phone' => '+91-98765-43212',
            'job_title' => 'VP Sales',
            'employment_type' => 'full-time',
            'salary' => 110000,
            'joined_on' => now()->subYears(3),
            'status' => 'active',
        ]);

        // HR Head - Reports to CEO
        $hrHead = Employee::firstOrCreate(['email' => 'mike.wilson@company.com'], [
            'full_name' => 'Mike Wilson',
            'department_id' => $hrDept->id,
            'manager_id' => $ceo->id,
            'phone' => '+91-98765-43213',
            'job_title' => 'VP Human Resources',
            'employment_type' => 'full-time',
            'salary' => 100000,
            'joined_on' => now()->subYears(3),
            'status' => 'active',
        ]);

        // Marketing Head - Reports to CEO
        $marketingHead = Employee::firstOrCreate(['email' => 'lisa.brown@company.com'], [
            'full_name' => 'Lisa Brown',
            'department_id' => $marketingDept->id,
            'manager_id' => $ceo->id,
            'phone' => '+91-98765-43214',
            'job_title' => 'VP Marketing',
            'employment_type' => 'full-time',
            'salary' => 95000,
            'joined_on' => now()->subYears(2),
            'status' => 'active',
        ]);

        // Engineering Team Members - Report to John Smith
        Employee::firstOrCreate(['email' => 'amit.patel@company.com'], [
            'full_name' => 'Amit Patel',
            'department_id' => $engineeringDept->id,
            'manager_id' => $engHead->id,
            'phone' => '+91-98765-43215',
            'job_title' => 'Senior PHP Developer',
            'employment_type' => 'full-time',
            'salary' => 75000,
            'joined_on' => now()->subYears(2),
            'status' => 'active',
        ]);

        Employee::firstOrCreate(['email' => 'priya.sharma@company.com'], [
            'full_name' => 'Priya Sharma',
            'department_id' => $engineeringDept->id,
            'manager_id' => $engHead->id,
            'phone' => '+91-98765-43216',
            'job_title' => 'Frontend Developer',
            'employment_type' => 'full-time',
            'salary' => 65000,
            'joined_on' => now()->subYears(1),
            'status' => 'active',
        ]);

        Employee::firstOrCreate(['email' => 'rajesh.kumar@company.com'], [
            'full_name' => 'Rajesh Kumar',
            'department_id' => $engineeringDept->id,
            'manager_id' => $engHead->id,
            'phone' => '+91-98765-43217',
            'job_title' => 'DevOps Engineer',
            'employment_type' => 'full-time',
            'salary' => 70000,
            'joined_on' => now()->subMonths(8),
            'status' => 'active',
        ]);

        // Sales Team Members - Report to Sarah Johnson
        Employee::firstOrCreate(['email' => 'arjun.nair@company.com'], [
            'full_name' => 'Arjun Nair',
            'department_id' => $salesDept->id,
            'manager_id' => $salesHead->id,
            'phone' => '+91-98765-43218',
            'job_title' => 'Sales Manager - Enterprise',
            'employment_type' => 'full-time',
            'salary' => 80000,
            'joined_on' => now()->subYears(2),
            'status' => 'active',
        ]);

        Employee::firstOrCreate(['email' => 'neha.reddy@company.com'], [
            'full_name' => 'Neha Reddy',
            'department_id' => $salesDept->id,
            'manager_id' => $salesHead->id,
            'phone' => '+91-98765-43219',
            'job_title' => 'Sales Executive',
            'employment_type' => 'full-time',
            'salary' => 55000,
            'joined_on' => now()->subMonths(6),
            'status' => 'active',
        ]);

        // Sales Team under Arjun
        Employee::firstOrCreate(['email' => 'vikram.singh@company.com'], [
            'full_name' => 'Vikram Singh',
            'department_id' => $salesDept->id,
            'manager_id' => $salesHead->id,
            'phone' => '+91-98765-43220',
            'job_title' => 'Account Executive',
            'employment_type' => 'full-time',
            'salary' => 50000,
            'joined_on' => now()->subMonths(4),
            'status' => 'active',
        ]);

        // HR Team Members - Report to Mike Wilson
        Employee::firstOrCreate(['email' => 'kavya.gupta@company.com'], [
            'full_name' => 'Kavya Gupta',
            'department_id' => $hrDept->id,
            'manager_id' => $hrHead->id,
            'phone' => '+91-98765-43221',
            'job_title' => 'HR Manager',
            'employment_type' => 'full-time',
            'salary' => 65000,
            'joined_on' => now()->subYears(1),
            'status' => 'active',
        ]);

        Employee::firstOrCreate(['email' => 'ananya.sen@company.com'], [
            'full_name' => 'Ananya Sen',
            'department_id' => $hrDept->id,
            'manager_id' => $hrHead->id,
            'phone' => '+91-98765-43222',
            'job_title' => 'Recruiter',
            'employment_type' => 'full-time',
            'salary' => 45000,
            'joined_on' => now()->subMonths(3),
            'status' => 'active',
        ]);

        // Marketing Team Members - Report to Lisa Brown
        Employee::firstOrCreate(['email' => 'ashok.desai@company.com'], [
            'full_name' => 'Ashok Desai',
            'department_id' => $marketingDept->id,
            'manager_id' => $marketingHead->id,
            'phone' => '+91-98765-43223',
            'job_title' => 'Digital Marketing Manager',
            'employment_type' => 'full-time',
            'salary' => 60000,
            'joined_on' => now()->subYears(1),
            'status' => 'active',
        ]);

        Employee::firstOrCreate(['email' => 'sneha.iyer@company.com'], [
            'full_name' => 'Sneha Iyer',
            'department_id' => $marketingDept->id,
            'manager_id' => $marketingHead->id,
            'phone' => '+91-98765-43224',
            'job_title' => 'Content Strategist',
            'employment_type' => 'full-time',
            'salary' => 55000,
            'joined_on' => now()->subMonths(10),
            'status' => 'active',
        ]);

        echo "✓ Organization hierarchy seeded successfully with 17 employees!";
    }
}
