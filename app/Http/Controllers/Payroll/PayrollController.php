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
            $structures = PayStructure::where('tenant_id', $tenantId)
                ->with('employee:id,full_name')
                ->get();

            $payslips = Payslip::where('tenant_id', $tenantId)
                ->with('employee:id,full_name')
                ->orderByDesc('created_at')
                ->get();

            $employees = Employee::where('tenant_id', $tenantId)
                ->whereDoesntHave('payStructure')
                ->get(['id', 'full_name']);

            return response()->json([
                'isAdmin' => true,
                'structures' => $structures,
                'payslips' => $payslips,
                'availableEmployees' => $employees,
            ]);
        }

        // Regular employee sees only their payslips
        $employee = Employee::where('email', $user->email)->where('tenant_id', $tenantId)->first();
        $payslips = $employee ? Payslip::where('employee_id', $employee->id)->orderByDesc('created_at')->get() : [];

        return response()->json([
            'isAdmin' => false,
            'payslips' => $payslips
        ]);
    }

    public function storeStructure(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:pay_structures,employee_id',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'deductions' => 'nullable|array',
        ]);

        $tenantId = TenantContext::id();
        $structure = PayStructure::create(array_merge($validated, ['tenant_id' => $tenantId]));

        return response()->json(['success' => true, 'structure' => $structure]);
    }

    public function generatePayslips(Request $request): JsonResponse
    {
        $request->validate(['month' => 'required|string']); // e.g., "2026-03"
        
        $tenantId = TenantContext::id();
        $date = Carbon::parse($request->month);
        $monthLabel = $date->format('F Y');

        $structures = PayStructure::where('tenant_id', $tenantId)->get();
        $count = 0;

        foreach ($structures as $struct) {
            // Avoid duplicate payslips for same month
            $exists = Payslip::where('employee_id', $struct->employee_id)
                ->where('month', $monthLabel)
                ->exists();
            
            if ($exists) continue;

            $totalAllowances = collect($struct->allowances)->sum('amount');
            $totalDeductions = collect($struct->deductions)->sum('amount');
            $netPay = $struct->base_salary + $totalAllowances - $totalDeductions;

            Payslip::create([
                'tenant_id' => $tenantId,
                'employee_id' => $struct->employee_id,
                'month' => $monthLabel,
                'period_start' => $date->copy()->startOfMonth(),
                'period_end' => $date->copy()->endOfMonth(),
                'base_salary' => $struct->base_salary,
                'total_allowances' => $totalAllowances,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
                'status' => 'draft',
                'details' => [
                    'allowances' => $struct->allowances,
                    'deductions' => $struct->deductions
                ]
            ]);
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
