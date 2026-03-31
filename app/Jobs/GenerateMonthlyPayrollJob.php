<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\PayStructure;
use App\Models\Payslip;
use App\Support\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyPayrollJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // Allow 10 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(public string $tenantId, public string $monthLabel)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Set context for models that might rely on it
        TenantContext::set($this->tenantId);

        $date = \Carbon\Carbon::parse($this->monthLabel);
        
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();
        $totalDaysInMonth = $periodStart->daysInMonth;

        $payrollPolicy   = \App\Models\PayrollPolicy::where('tenant_id', $this->tenantId)->where('is_active', true)->first();
        $attendancePolicy = \App\Models\AttendancePolicy::where('tenant_id', $this->tenantId)->where('is_active', true)->first();
        $overtimePolicy  = \App\Models\OvertimePolicy::where('tenant_id', $this->tenantId)->where('is_active', true)->first();
        $holidayPolicy   = \App\Models\HolidayPolicy::where('tenant_id', $this->tenantId)->where('is_active', true)->first();

        $prorateOnJoin = $payrollPolicy ? $payrollPolicy->prorate_on_join : true;
        $prorateOnExit = $payrollPolicy ? $payrollPolicy->prorate_on_exit : true;
        $standardHoursPerDay = $attendancePolicy && $attendancePolicy->standard_hours_per_day > 0 
            ? (float) $attendancePolicy->standard_hours_per_day 
            : 8.0;

        $configuredWeekendDays = $holidayPolicy && !empty($holidayPolicy->weekend_days)
            ? array_map('strtolower', $holidayPolicy->weekend_days)
            : ['saturday', 'sunday'];

        $structures = PayStructure::where('tenant_id', $this->tenantId)
            ->with('employee:id,full_name,status,joined_on,last_working_day')
            ->get();
            
        $count = 0;

        foreach ($structures as $struct) {
            $exists = Payslip::where('employee_id', $struct->employee_id)
                ->where('month', $this->monthLabel)
                ->exists();

            if ($exists) continue;

            $employee = $struct->employee;
            if (!$employee) continue;

            $effectiveStart = $periodStart->copy();
            $effectiveEnd = $periodEnd->copy();
            $isProrated = false;
            $prorateReason = null;

            if ($prorateOnJoin && $employee->joined_on && $employee->joined_on->gt($periodStart) && $employee->joined_on->lte($periodEnd)) {
                $effectiveStart = $employee->joined_on->copy();
                $isProrated = true;
                $prorateReason = 'Joined mid-month on ' . $employee->joined_on->format('d M Y');
            }

            if ($prorateOnExit && $employee->status === 'resigned' && $employee->last_working_day) {
                if ($employee->last_working_day->lt($periodStart)) continue;
                if ($employee->last_working_day->lt($periodEnd)) {
                    $effectiveEnd = $employee->last_working_day->copy();
                    $isProrated = true;
                    $prorateReason = ($prorateReason ? $prorateReason . '; ' : '')
                        . 'Resigned — last working day ' . $employee->last_working_day->format('d M Y');
                }
            }

            $workedDays = $effectiveStart->diffInDays($effectiveEnd) + 1;
            $prorateRatio = $isProrated ? ($workedDays / $totalDaysInMonth) : 1;

            $monthlyBase = $struct->base_salary / 12;
            $proratedBaseSalary = round($monthlyBase * $prorateRatio, 2);
            $dailyRate = $monthlyBase / $totalDaysInMonth;
            $hourlyRate = $standardHoursPerDay > 0 ? ($dailyRate / $standardHoursPerDay) : 0;

            $unpaidLeaves = \App\Models\LeaveRequest::where('employee_id', $struct->employee_id)
                ->where('status', 'approved')
                ->where('leave_type', 'unpaid')
                ->where('start_date', '<=', $effectiveEnd)
                ->where('end_date', '>=', $effectiveStart)
                ->get();

            $unpaidDays = 0;
            foreach ($unpaidLeaves as $leave) {
                $start = \Carbon\Carbon::parse($leave->start_date)->max($effectiveStart);
                $end   = \Carbon\Carbon::parse($leave->end_date)->min($effectiveEnd);
                $unpaidDays += $start->diffInDays($end) + 1;
            }

            $unpaidDeduction = round($unpaidDays * $dailyRate, 2);

            $overtimePay = 0;
            $overtimeHoursTotal = 0;

            if ($overtimePolicy && $hourlyRate > 0) {
                $attendances = \App\Models\AttendanceRecord::where('employee_id', $struct->employee_id)
                    ->whereBetween('attendance_date', [$effectiveStart, $effectiveEnd])
                    ->get();

                $minSeconds = ($overtimePolicy->minimum_minutes ?? 0) * 60;
                $standardSeconds = $standardHoursPerDay * 3600;

                foreach ($attendances as $att) {
                    $workedSeconds = $att->total_work_seconds ?? 0;
                    if ($workedSeconds > $standardSeconds) {
                        $extraSeconds = $workedSeconds - $standardSeconds;
                        if ($extraSeconds >= $minSeconds) {
                            $extraHours = $extraSeconds / 3600;
                            $attDate = \Carbon\Carbon::parse($att->attendance_date);

                            $isWeekend = in_array(strtolower($attDate->format('l')), $configuredWeekendDays);
                            $multiplier = $isWeekend ? ($overtimePolicy->weekend_multiplier ?? 1.5) : ($overtimePolicy->weekday_multiplier ?? 1.25);
                            
                            $overtimePay += ($extraHours * $hourlyRate * $multiplier);
                            $overtimeHoursTotal += $extraHours;
                        }
                    }
                }
                
                if ($overtimePolicy->max_hours_per_month > 0 && $overtimeHoursTotal > $overtimePolicy->max_hours_per_month) {
                    $ratio = $overtimePolicy->max_hours_per_month / $overtimeHoursTotal;
                    $overtimePay = $overtimePay * $ratio;
                    $overtimeHoursTotal = (float) $overtimePolicy->max_hours_per_month;
                }
                
                $overtimePay = round($overtimePay, 2);
            }

            $totalAllowances = round(collect($struct->allowances)->sum('amount') * $prorateRatio, 2);
            $totalDeductions = round(collect($struct->deductions)->sum('amount') * $prorateRatio, 2) + $unpaidDeduction;

            if ($overtimePay > 0) {
                $totalAllowances += $overtimePay;
            }

            $adjustments = \App\Models\PayrollAdjustment::where('employee_id', $struct->employee_id)
                ->where('status', 'pending')
                ->where(function ($q) {
                    $q->whereNull('month')->orWhere('month', $this->monthLabel);
                })
                ->get();

            $adjustmentAdditions  = $adjustments->where('type', 'addition')->sum('amount');
            $adjustmentDeductions = $adjustments->where('type', 'deduction')->sum('amount');

            $netPay = $proratedBaseSalary + $totalAllowances - $totalDeductions
                    + $adjustmentAdditions - $adjustmentDeductions;

            $allowancesList = collect($struct->allowances)->map(function($a) use ($prorateRatio) {
                return ['name' => $a['name'], 'amount' => round($a['amount'] * $prorateRatio, 2)];
            })->toArray();

            $deductionsList = collect($struct->deductions)->map(function($d) use ($prorateRatio) {
                return ['name' => $d['name'], 'amount' => round($d['amount'] * $prorateRatio, 2)];
            })->toArray();

            if ($overtimePay > 0) {
                $allowancesList[] = [
                    'name' => 'Overtime (' . round($overtimeHoursTotal, 1) . ' hrs)',
                    'amount' => $overtimePay
                ];
            }

            $details = [
                'allowances' => array_values($allowancesList),
                'deductions' => array_values($deductionsList),
            ];

            if ($adjustments->isNotEmpty()) {
                $details['adjustments'] = $adjustments->map(fn ($a) => [
                    'label'  => $a->label,
                    'type'   => $a->type,
                    'amount' => $a->amount,
                ])->values()->all();
            }

            if ($isProrated) {
                $details['proration'] = [
                    'reason' => $prorateReason,
                    'worked_days' => $workedDays,
                    'total_days' => $totalDaysInMonth,
                    'ratio' => round($prorateRatio, 4),
                ];
            }

            if ($unpaidDeduction > 0) {
                $details['unpaid_leave_deduction'] = [
                    'days' => $unpaidDays,
                    'amount' => $unpaidDeduction,
                ];
            }

            Payslip::create([
                'tenant_id'        => $this->tenantId,
                'employee_id'      => $struct->employee_id,
                'month'            => $this->monthLabel,
                'period_start'     => $effectiveStart,
                'period_end'       => $effectiveEnd,
                'base_salary'      => $proratedBaseSalary,
                'total_allowances' => $totalAllowances + $adjustmentAdditions,
                'total_deductions' => $totalDeductions + $adjustmentDeductions,
                'net_pay'          => max(0, $netPay),
                'status'           => 'draft',
                'details'          => $details,
            ]);

            if ($adjustments->isNotEmpty()) {
                \App\Models\PayrollAdjustment::whereIn('id', $adjustments->pluck('id'))
                    ->update(['status' => 'included', 'month' => $this->monthLabel]);
            }

            $count++;
        }
        
        Log::info("Payroll generation completed. Created {$count} payslips for month {$this->monthLabel}.");
    }
}
