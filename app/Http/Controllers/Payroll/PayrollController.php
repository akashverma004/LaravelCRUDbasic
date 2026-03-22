<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PayStructure;
use App\Models\Payslip;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(): View
    {
        return view('hrms.payroll.index');
    }

    public function data(Request $request): JsonResponse
    {
        $tenantId = TenantContext::id();
        $user = auth()->user();

        // Admin/HR see all structures and payslips
        if ($user->hasAnyRole(['admin', 'hr_manager'])) {
            $payslips = Payslip::where('tenant_id', $tenantId)
                ->with('employee:id,full_name')
                ->orderByDesc('created_at')
                ->get();

            // ALL employees with their pay structure (null if not configured yet)
            $employees = Employee::where('tenant_id', $tenantId)
                ->with('payStructure')
                ->orderBy('full_name')
                ->get(['id', 'full_name', 'job_title', 'status', 'last_working_day', 'joined_on']);

            // Summary stats
            $stats = [
                'totalEmployees' => $employees->filter(fn($e) => $e->payStructure !== null)->count(),
                'totalPayroll'   => $payslips->where('status', 'paid')->sum('net_pay'),
                'draftCount'     => $payslips->where('status', 'draft')->count(),
                'paidCount'      => $payslips->where('status', 'paid')->count(),
            ];

            return response()->json([
                'isAdmin'   => true,
                'employees' => $employees,
                'payslips'  => $payslips,
                'stats'     => $stats,
            ]);
        }

        // Regular employee sees only their payslips
        $employee = Employee::where('email', $user->email)->where('tenant_id', $tenantId)->first();
        $payslips = $employee ? Payslip::where('employee_id', $employee->id)->orderByDesc('created_at')->get() : [];

        return response()->json([
            'isAdmin' => false,
            'payslips' => $payslips,
        ]);
    }

    public function storeStructure(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id'         => 'required|exists:employees,id',
            'base_salary'         => 'required|numeric|min:0',
            'allowances'          => 'nullable|array',
            'allowances.*.name'   => 'required_with:allowances|string|max:100',
            'allowances.*.amount' => 'required_with:allowances|numeric|min:0',
            'deductions'          => 'nullable|array',
            'deductions.*.name'   => 'required_with:deductions|string|max:100',
            'deductions.*.amount' => 'required_with:deductions|numeric|min:0',
        ]);

        $tenantId = TenantContext::id();

        // updateOrCreate — works for both "Set Up" (new) and "Edit" (existing)
        $structure = PayStructure::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'tenant_id' => $tenantId],
            [
                'base_salary' => $validated['base_salary'],
                'allowances'  => $validated['allowances'] ?? [],
                'deductions'  => $validated['deductions'] ?? [],
            ]
        );

        $structure->load('employee:id,full_name,job_title,status,last_working_day');

        return response()->json(['success' => true, 'structure' => $structure]);
    }

    public function updateStructure(Request $request, PayStructure $payStructure): JsonResponse
    {
        $validated = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'allowances.*.name' => 'required_with:allowances|string|max:100',
            'allowances.*.amount' => 'required_with:allowances|numeric|min:0',
            'deductions' => 'nullable|array',
            'deductions.*.name' => 'required_with:deductions|string|max:100',
            'deductions.*.amount' => 'required_with:deductions|numeric|min:0',
        ]);

        $payStructure->update($validated);
        $payStructure->load('employee:id,full_name,job_title,status,last_working_day');

        return response()->json(['success' => true, 'structure' => $payStructure]);
    }

    public function destroyStructure(PayStructure $payStructure): JsonResponse
    {
        $payStructure->delete();
        return response()->json(['success' => true]);
    }

    public function generatePayslips(Request $request): JsonResponse
    {
        $request->validate(['month' => 'required|string']); // e.g., "2026-03"

        $tenantId = TenantContext::id();
        $date = \Carbon\Carbon::parse($request->month);
        $monthLabel = $date->format('F Y');

        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();
        $totalDaysInMonth = $periodStart->daysInMonth;

        // Fetch active policies
        $payrollPolicy = \App\Models\PayrollPolicy::where('tenant_id', $tenantId)->where('is_active', true)->first();
        $attendancePolicy = \App\Models\AttendancePolicy::where('tenant_id', $tenantId)->where('is_active', true)->first();
        $overtimePolicy = \App\Models\OvertimePolicy::where('tenant_id', $tenantId)->where('is_active', true)->first();

        $prorateOnJoin = $payrollPolicy ? $payrollPolicy->prorate_on_join : true;
        $prorateOnExit = $payrollPolicy ? $payrollPolicy->prorate_on_exit : true;
        $standardHoursPerDay = $attendancePolicy ? (float) $attendancePolicy->standard_hours_per_day : 8.0;

        $structures = PayStructure::where('tenant_id', $tenantId)
            ->with('employee:id,full_name,status,joined_on,last_working_day')
            ->get();
        $count = 0;

        foreach ($structures as $struct) {
            // Avoid duplicate payslips for same month
            $exists = Payslip::where('employee_id', $struct->employee_id)
                ->where('month', $monthLabel)
                ->exists();

            if ($exists) continue;

            $employee = $struct->employee;
            if (!$employee) continue;

            // ── Determine effective working period within the month ──
            $effectiveStart = $periodStart->copy();
            $effectiveEnd = $periodEnd->copy();
            $isProrated = false;
            $prorateReason = null;

            // If employee joined mid-month, prorate from their joining date (if policy allows)
            if ($prorateOnJoin && $employee->joined_on && $employee->joined_on->gt($periodStart) && $employee->joined_on->lte($periodEnd)) {
                $effectiveStart = $employee->joined_on->copy();
                $isProrated = true;
                $prorateReason = 'Joined mid-month on ' . $employee->joined_on->format('d M Y');
            }

            // If employee resigned and has a last_working_day within the pay period, prorate (if policy allows)
            if ($prorateOnExit && $employee->status === 'resigned' && $employee->last_working_day) {
                if ($employee->last_working_day->lt($periodStart)) {
                    // Employee's last day was before this month — skip payslip entirely
                    continue;
                }
                if ($employee->last_working_day->lt($periodEnd)) {
                    $effectiveEnd = $employee->last_working_day->copy();
                    $isProrated = true;
                    $prorateReason = ($prorateReason ? $prorateReason . '; ' : '')
                        . 'Resigned — last working day ' . $employee->last_working_day->format('d M Y');
                }
            }

            // Calculate worked days for proration
            $workedDays = $effectiveStart->diffInDays($effectiveEnd) + 1;
            $prorateRatio = $isProrated ? ($workedDays / $totalDaysInMonth) : 1;

            // Prorated base salary
            $proratedBaseSalary = round($struct->base_salary * $prorateRatio, 2);
            $dailyRate = $struct->base_salary / $totalDaysInMonth;
            $hourlyRate = $standardHoursPerDay > 0 ? ($dailyRate / $standardHoursPerDay) : 0;

            // Calculate unpaid leave deductions (only within effective period)
            $unpaidLeaves = \App\Models\LeaveRequest::where('employee_id', $struct->employee_id)
                ->where('status', 'approved')
                ->where('leave_type', 'unpaid')
                ->where(function ($q) use ($effectiveStart, $effectiveEnd) {
                    $q->whereBetween('start_date', [$effectiveStart, $effectiveEnd])
                      ->orWhereBetween('end_date', [$effectiveStart, $effectiveEnd]);
                })->get();

            $unpaidDays = 0;
            foreach ($unpaidLeaves as $leave) {
                $start = \Carbon\Carbon::parse($leave->start_date)->max($effectiveStart);
                $end = \Carbon\Carbon::parse($leave->end_date)->min($effectiveEnd);
                $unpaidDays += $start->diffInDays($end) + 1;
            }

            $unpaidDeduction = round($unpaidDays * $dailyRate, 2);

            // ── Overtime Calculation ──
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
                            $date = \Carbon\Carbon::parse($att->attendance_date);
                            
                            // Check if weekend (could also check HolidayPolicy)
                            $isWeekend = $date->isWeekend();
                            $multiplier = $isWeekend ? ($overtimePolicy->weekend_multiplier ?? 1.5) : ($overtimePolicy->weekday_multiplier ?? 1.25);
                            
                            $overtimePay += ($extraHours * $hourlyRate * $multiplier);
                            $overtimeHoursTotal += $extraHours;
                        }
                    }
                }
                
                // Cap by max hours per month if set
                if ($overtimePolicy->max_hours_per_month > 0 && $overtimeHoursTotal > $overtimePolicy->max_hours_per_month) {
                    // Recalculate roughly via average rate, or specifically cap the hours to `max_hours_per_month`.
                    // For simplicity here, cap the pay proportionally:
                    $ratio = $overtimePolicy->max_hours_per_month / $overtimeHoursTotal;
                    $overtimePay = $overtimePay * $ratio;
                    $overtimeHoursTotal = (float) $overtimePolicy->max_hours_per_month;
                }
                
                $overtimePay = round($overtimePay, 2);
            }

            // Apply proration to regular allowances & deductions
            $totalAllowances = round(collect($struct->allowances)->sum('amount') * $prorateRatio, 2);
            $totalDeductions = round(collect($struct->deductions)->sum('amount') * $prorateRatio, 2) + $unpaidDeduction;

            // Fold in Overtime
            if ($overtimePay > 0) {
                $totalAllowances += $overtimePay;
            }

            // ── One-time adjustments (approved reimbursements, bonuses, etc.) ──
            $adjustments = \App\Models\PayrollAdjustment::where('employee_id', $struct->employee_id)
                ->where('status', 'pending')
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
                'tenant_id'        => $tenantId,
                'employee_id'      => $struct->employee_id,
                'month'            => $monthLabel,
                'period_start'     => $periodStart,
                'period_end'       => $effectiveEnd,
                'base_salary'      => $proratedBaseSalary,
                'total_allowances' => $totalAllowances + $adjustmentAdditions,
                'total_deductions' => $totalDeductions + $adjustmentDeductions,
                'net_pay'          => max(0, $netPay),
                'status'           => 'draft',
                'details'          => $details,
            ]);

            // Mark adjustments as included so they aren't counted again
            if ($adjustments->isNotEmpty()) {
                \App\Models\PayrollAdjustment::whereIn('id', $adjustments->pluck('id'))
                    ->update(['status' => 'included', 'month' => $monthLabel]);
            }

            $count++;
        }

        return response()->json(['success' => true, 'message' => "Generated $count payslips for $monthLabel."]);
    }

    public function markAsPaid(Payslip $payslip): JsonResponse
    {
        $payslip->update(['status' => 'paid']);
        return response()->json(['success' => true]);
    }
}
