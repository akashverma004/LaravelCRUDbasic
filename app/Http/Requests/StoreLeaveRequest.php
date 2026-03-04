<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Models\LeavePolicy;
use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::id() ?? (int) auth()->user()?->tenant_id ?: 1;

        return [
            'employee_id' => ['required', 'integer', Rule::exists('employees', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'leave_type' => ['required', 'in:annual,sick,casual,unpaid'],
            'leave_session' => ['required', 'in:full_day,morning,evening'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:500'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $employeeId = (int) $this->input('employee_id');
            $leaveType = (string) $this->input('leave_type');
            $leaveSession = (string) $this->input('leave_session', 'full_day');
            $startDateInput = $this->input('start_date');
            $endDateInput = $this->input('end_date');

            if (! $employeeId || ! $leaveType || ! $startDateInput || ! $endDateInput) {
                return;
            }

            try {
                $startDate = Carbon::parse($startDateInput);
                $endDate = Carbon::parse($endDateInput);
            } catch (\Throwable) {
                return;
            }

            if ($leaveSession !== 'full_day' && ! $startDate->isSameDay($endDate)) {
                $validator->errors()->add('leave_session', 'Morning/evening leave must be for a single day.');
                return;
            }

            $requestedDays = $leaveSession === 'full_day'
                ? $startDate->diffInDays($endDate) + 1
                : 0.5;

            $employee = Employee::with(['leavePolicy', 'leaveRequests'])->find($employeeId);
            if (! $employee) {
                return;
            }

            $policy = LeavePolicy::query()->first();
            $defaultLimits = [
                'annual' => 12,
                'sick' => 8,
                'casual' => 6,
                'unpaid' => 0,
            ];

            $limit = match ($leaveType) {
                'annual' => (float) ($policy->annual_limit ?? $defaultLimits['annual']),
                'sick' => (float) ($policy->sick_limit ?? $defaultLimits['sick']),
                'casual' => (float) ($policy->casual_limit ?? $defaultLimits['casual']),
                'unpaid' => (float) ($policy->unpaid_limit ?? $defaultLimits['unpaid']),
                default => 0,
            };

            $usedDays = (float) $employee->leaveRequests
                ->where('status', 'approved')
                ->where('leave_type', $leaveType)
                ->sum(function ($leave) {
                    $session = $leave->leave_session ?? 'full_day';
                    if ($session !== 'full_day') {
                        return 0.5;
                    }

                    return $leave->start_date->diffInDays($leave->end_date) + 1;
                });

            if (($usedDays + $requestedDays) > $limit) {
                $remaining = max(0, $limit - $usedDays);
                $validator->errors()->add(
                    'leave_type',
                    'Leave limit exceeded for ' . ucfirst($leaveType) . '. Remaining: ' . rtrim(rtrim(number_format($remaining, 1), '0'), '.') . ' day(s).'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Employee selection is required.',
            'start_date.after_or_equal' => 'Leave start date must be today or later.',
            'end_date.after_or_equal' => 'Leave end date must be after or equal to start date.',
            'reason.required' => 'Leave reason is required.',
        ];
    }
}
