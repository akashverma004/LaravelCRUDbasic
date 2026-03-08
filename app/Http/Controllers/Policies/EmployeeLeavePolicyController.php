<?php

namespace App\Http\Controllers\Policies;

use App\Http\Controllers\Controller;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeLeavePolicyController extends Controller
{
    public function edit(int $id): View
    {
        $employee = Employee::with(['department', 'leavePolicy'])->findOrFail($id);

        $policy = $employee->leavePolicy ?? (object) [
            'annual_limit' => 12,
            'sick_limit' => 8,
            'casual_limit' => 6,
            'unpaid_limit' => 0,
        ];

        return view('hrms.employees.leave-policy', compact('employee', 'policy'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'annual_limit' => ['required', 'integer', 'min:0', 'max:365'],
            'sick_limit' => ['required', 'integer', 'min:0', 'max:365'],
            'casual_limit' => ['required', 'integer', 'min:0', 'max:365'],
            'unpaid_limit' => ['required', 'integer', 'min:0', 'max:365'],
        ]);

        $employee = Employee::findOrFail($id);
        $employee->leavePolicy()->updateOrCreate(
            ['employee_id' => $employee->id],
            $validated
        );

        return redirect()
            ->route('employees.leave-policy.edit', $employee->id)
            ->with('status', 'Leave policy updated successfully.');
    }
}
