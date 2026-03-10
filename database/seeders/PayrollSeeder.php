<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\PayStructure;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = Tenant::first()->id ?? 1;
        $employees = Employee::where('tenant_id', $tenantId)->limit(5)->get();

        foreach ($employees as $emp) {
            PayStructure::create([
                'tenant_id' => $tenantId,
                'employee_id' => $emp->id,
                'base_salary' => rand(4000, 8000),
                'allowances' => [
                    ['name' => 'Housing Allowance', 'amount' => 500],
                    ['name' => 'Transport', 'amount' => 200],
                ],
                'deductions' => [
                    ['name' => 'Income Tax', 'amount' => 450],
                    ['name' => 'Insurance', 'amount' => 100],
                ],
                'currency' => 'USD',
            ]);
        }
    }
}
