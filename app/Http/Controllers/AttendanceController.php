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

        $message = 'Punched in successfully at ' . now()->format('H:i');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
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
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not currenty clocked in.'], 422);
            }
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

        $message = 'Paused work for ' . ucfirst($type) . ' at ' . now()->format('H:i');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
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
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not on a break.'], 422);
            }
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

        $message = 'Resumed work at ' . now()->format('H:i');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
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
            
            $message = 'Shift marked as completed at ' . now()->format('H:i');

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return back()->with('success', $message);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Could not complete shift.'], 422);
        }

        return back()->with('error', 'Could not complete shift.');
    }
}
