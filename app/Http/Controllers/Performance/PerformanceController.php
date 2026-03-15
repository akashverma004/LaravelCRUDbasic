<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Goal;
use App\Models\OneOnOneNote;
use App\Models\PerformanceReview;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    public function index(): View
    {
        return view('hrms.performance.index');
    }

    public function data(Request $request): JsonResponse
    {
        $tenantId = TenantContext::id();
        $user = auth()->user();
        
        // Identify the employee record for the current user
        $employee = Employee::where('email', $user->email)
            ->where('tenant_id', $tenantId)
            ->first();

        // Goals
        $goalsQuery = Goal::where('tenant_id', $tenantId);
        if (!$user->hasAnyRole(['admin', 'hr_manager'])) {
            $goalsQuery->where('employee_id', $employee?->id ?? 0);
        }
        $goals = $goalsQuery->orderByDesc('created_at')->get();

        // Reviews
        $reviewsQuery = PerformanceReview::where('tenant_id', $tenantId);
        if (!$user->hasAnyRole(['admin', 'hr_manager'])) {
            $reviewsQuery->where('employee_id', $employee?->id ?? 0);
        }
        $reviews = $reviewsQuery->with(['reviewer:id,name', 'employee:id,full_name'])
            ->orderByDesc('created_at')
            ->get();

        // 1-on-1 Notes
        $notesQuery = OneOnOneNote::where('tenant_id', $tenantId);
        if (!$user->hasAnyRole(['admin', 'hr_manager'])) {
            $notesQuery->where(function($q) use ($employee, $user) {
                $q->where('employee_id', $employee?->id ?? 0)
                  ->orWhere('manager_id', $user->id);
            });
        }
        $notes = $notesQuery->with(['manager:id,name', 'employee:id,full_name'])
            ->orderByDesc('meeting_date')
            ->get();

        $employees = [];
        if ($user->hasAnyRole(['admin', 'hr_manager'])) {
            $employees = Employee::where('tenant_id', $tenantId)->get(['id', 'full_name']);
        }

        return response()->json([
            'goals' => $goals,
            'reviews' => $reviews,
            'notes' => $notes,
            'is_manager' => $user->hasAnyRole(['admin', 'hr_manager']),
            'employees' => $employees,
        ]);
    }

    // --- GOALS CRUD ---
    public function storeGoal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $tenantId = TenantContext::id();
        $user = auth()->user();

        // If not admin, force goal to current user's employee record
        if (!$user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = Employee::where('email', $user->email)->where('tenant_id', $tenantId)->first();
            $validated['employee_id'] = $employee?->id;
        }

        $goal = Goal::create(array_merge($validated, ['tenant_id' => $tenantId]));

        return response()->json(['success' => true, 'goal' => $goal]);
    }

    public function updateGoal(Request $request, Goal $goal): JsonResponse
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'status' => 'required|in:active,completed,archived',
        ]);

        $goal->update($validated);
        return response()->json(['success' => true]);
    }

    // --- REVIEWS CRUD ---
    public function storeReview(Request $request): JsonResponse
    {
        if (!auth()->user()->hasAnyRole(['admin', 'hr_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'review_cycle' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'required|string',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'status' => 'required|in:draft,submitted',
        ]);

        $review = PerformanceReview::create(array_merge($validated, [
            'tenant_id' => TenantContext::id(),
            'reviewer_id' => auth()->id(),
        ]));

        return response()->json(['success' => true, 'review' => $review]);
    }

    // --- 1-on-1 NOTES CRUD ---
    public function storeNote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'meeting_date' => 'required|date',
            'talking_points' => 'required|string',
            'action_items' => 'nullable|string',
            'private_notes' => 'nullable|string',
        ]);

        $note = OneOnOneNote::create(array_merge($validated, [
            'tenant_id' => TenantContext::id(),
            'manager_id' => auth()->id(),
        ]));

        return response()->json(['success' => true, 'note' => $note]);
    }
}
