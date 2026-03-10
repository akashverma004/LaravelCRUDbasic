<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOnboarding;
use App\Models\EmployeeOnboardingTask;
use App\Models\OnboardingTemplate;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(): View
    {
        return view('hrms.onboarding.index');
    }

    public function data(): JsonResponse
    {
        $tenantId = TenantContext::id();
        $user = auth()->user();
        
        // Find employee for current user
        $employee = Employee::where('email', $user->email)
            ->where('tenant_id', $tenantId)
            ->first();

        // Admin/HR see all onboardings
        if ($user->hasAnyRole(['admin', 'hr_manager'])) {
            $onboardings = EmployeeOnboarding::where('tenant_id', $tenantId)
                ->with(['employee:id,full_name', 'tasks'])
                ->get()
                ->map(fn($o) => [
                    'id' => $o->id,
                    'employee_name' => $o->employee->full_name,
                    'status' => $o->status,
                    'progress' => $o->progress,
                    'started_at' => $o->started_at->format('d M Y'),
                ]);

            $templates = OnboardingTemplate::where('tenant_id', $tenantId)
                ->with('tasks')
                ->get();
            
            $employees = Employee::where('tenant_id', $tenantId)
                ->whereDoesntHave('onboardings', fn($q) => $q->where('status', 'in_progress'))
                ->get(['id', 'full_name']);

            return response()->json([
                'isAdmin' => true,
                'onboardings' => $onboardings,
                'templates' => $templates,
                'availableEmployees' => $employees,
            ]);
        }

        // Regular employee sees their own active onboarding
        $activeOnboarding = null;
        if ($employee) {
            $activeOnboarding = EmployeeOnboarding::where('employee_id', $employee->id)
                ->where('tenant_id', $tenantId)
                ->where('status', 'in_progress')
                ->with('tasks')
                ->first();
        }

        return response()->json([
            'isAdmin' => false,
            'onboarding' => $activeOnboarding ? [
                'id' => $activeOnboarding->id,
                'progress' => $activeOnboarding->progress,
                'tasks' => $activeOnboarding->tasks,
            ] : null
        ]);
    }

    public function completeTask(Request $request, EmployeeOnboardingTask $task): JsonResponse
    {
        $task->update([
            'is_completed' => $request->is_completed,
            'completed_at' => $request->is_completed ? now() : null
        ]);

        // Check if all tasks are complete
        $onboarding = $task->onboarding;
        if ($onboarding->tasks()->where('is_completed', false)->count() === 0) {
            $onboarding->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        } else {
            $onboarding->update(['status' => 'in_progress', 'completed_at' => null]);
        }

        return response()->json([
            'success' => true,
            'onboarding_status' => $onboarding->status,
            'progress' => $onboarding->progress
        ]);
    }

    public function assign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'template_id' => 'required|exists:onboarding_templates,id',
        ]);

        $tenantId = TenantContext::id();
        $template = OnboardingTemplate::where('tenant_id', $tenantId)->findOrFail($validated['template_id']);

        $onboarding = EmployeeOnboarding::create([
            'tenant_id' => $tenantId,
            'employee_id' => $validated['employee_id'],
            'template_id' => $template->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        foreach ($template->tasks as $tTask) {
            EmployeeOnboardingTask::create([
                'onboarding_id' => $onboarding->id,
                'title' => $tTask->title,
                'description' => $tTask->description,
                'is_completed' => false,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
