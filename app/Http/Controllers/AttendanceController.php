<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function punchIn(Request $request)
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->firstOrFail();
        $today = now()->toDateString();
        $now = now()->toDateTimeString();

        $record = AttendanceRecord::firstOrCreate(
            ['employee_id' => $employee->id, 'attendance_date' => $today],
            [
                'tenant_id' => $user->tenant_id,
                'clock_in_at' => now()->toTimeString(), 
                'work_mode' => 'onsite',
                'status' => 'clocked_in',
                'intervals' => [
                    ['type' => 'work', 'start' => $now, 'end' => null]
                ]
            ]
        );

        // Safety: If record already existed without intervals (legacy), initialize them
        if (!$record->wasRecentlyCreated && (!$record->intervals || empty($record->intervals))) {
            $record->update([
                'status' => 'clocked_in',
                'intervals' => [
                    ['type' => 'work', 'start' => $now, 'end' => null]
                ]
            ]);
        }

        $record->refresh()->updateCalculatedSeconds();

        return back()->with('success', 'Punched in successfully at ' . now()->format('H:i'));
    }

    public function pause(Request $request)
    {
        $type = $request->input('type', 'break'); // lunch or break
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->firstOrFail();
        $today = now()->toDateString();

        $record = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->firstOrFail();

        if ($record->status !== 'clocked_in') {
            return back()->with('error', 'You are not currenty clocked in.');
        }

        $intervals = $record->intervals ?? [];
        // End the current work interval
        foreach ($intervals as &$interval) {
            if ($interval['type'] === 'work' && $interval['end'] === null) {
                $interval['end'] = now()->toDateTimeString();
                break;
            }
        }

        // Start the break interval
        $intervals[] = ['type' => $type, 'start' => now()->toDateTimeString(), 'end' => null];

        $record->update([
            'status' => 'on_' . $type,
            'intervals' => $intervals
        ]);
        
        $record->refresh()->updateCalculatedSeconds();

        return back()->with('success', 'Paused work for ' . ucfirst($type) . ' at ' . now()->format('H:i'));
    }

    public function resume(Request $request)
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->firstOrFail();
        $today = now()->toDateString();

        $record = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->firstOrFail();

        if (strpos($record->status, 'on_') !== 0) {
            return back()->with('error', 'You are not on a break.');
        }

        $intervals = $record->intervals ?? [];
        // End the current break interval
        foreach ($intervals as &$interval) {
            if (strpos($record->status, $interval['type']) !== false && $interval['end'] === null) {
                $interval['end'] = now()->toDateTimeString();
                break;
            }
        }

        // Start new work interval
        $intervals[] = ['type' => 'work', 'start' => now()->toDateTimeString(), 'end' => null];

        $record->update([
            'status' => 'clocked_in',
            'intervals' => $intervals
        ]);

        $record->refresh()->updateCalculatedSeconds();

        return back()->with('success', 'Resumed work at ' . now()->format('H:i'));
    }

    public function punchOut(Request $request)
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->firstOrFail();
        $today = now()->toDateString();

        $record = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();

        if ($record && $record->status !== 'completed') {
            $intervals = $record->intervals ?? [];
            // End whatever is currently running
            foreach ($intervals as &$interval) {
                if ($interval['end'] === null) {
                    $interval['end'] = now()->toDateTimeString();
                    break;
                }
            }

            $record->update([
                'clock_out_at' => now()->toTimeString(),
                'status' => 'completed',
                'intervals' => $intervals
            ]);
            
            $record->refresh()->updateCalculatedSeconds();
            
            return back()->with('success', 'Shift marked as completed at ' . now()->format('H:i'));
        }

        return back()->with('error', 'Could not complete shift.');
    }
}
