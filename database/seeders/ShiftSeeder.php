<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = Tenant::first()->id ?? 1;

        $morning = Shift::create([
            'tenant_id' => $tenantId,
            'name' => 'Morning Shift',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'color' => '#0ea5e9'
        ]);

        $evening = Shift::create([
            'tenant_id' => $tenantId,
            'name' => 'Evening Shift',
            'start_time' => '16:00:00',
            'end_time' => '00:00:00',
            'color' => '#f59e0b'
        ]);

        $night = Shift::create([
            'tenant_id' => $tenantId,
            'name' => 'Night Shift',
            'start_time' => '00:00:00',
            'end_time' => '08:00:00',
            'color' => '#6366f1'
        ]);

        // Assign some shifts for the current week
        $employees = Employee::where('tenant_id', $tenantId)->limit(5)->get();
        $date = now()->startOfWeek();

        foreach ($employees as $idx => $emp) {
            ShiftSchedule::create([
                'tenant_id' => $tenantId,
                'employee_id' => $emp->id,
                'shift_id' => ($idx % 2 === 0) ? $morning->id : $evening->id,
                'date' => $date->copy()->addDays(rand(0, 4)),
            ]);
        }
    }
}
