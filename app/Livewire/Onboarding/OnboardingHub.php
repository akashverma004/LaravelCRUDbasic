<?php

namespace App\Livewire\Onboarding;

use App\Models\Employee;
use App\Models\EmployeeOnboarding;
use App\Models\EmployeeOnboardingTask;
use App\Models\OnboardingTemplate;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('hrms.layouts.app')]
#[Title('Onboarding Hub - PeopleFlow HRMS')]
class OnboardingHub extends Component
{
    public $activeOnboarding = null;
    public $onboardings = [];
    public $templates = [];
    public $availableEmployees = [];
    public bool $isAdmin = false;

    // Assignment Form
    public $selectedEmployeeId = '';
    public $selectedTemplateId = '';
    public bool $showAssignModal = false;

    public function mount()
    {
        $user = Auth::user();
        $this->isAdmin = $user->hasAnyRole(['admin', 'hr_manager']);
        $this->loadData();
    }

    public function loadData()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        if ($this->isAdmin) {
            $this->onboardings = EmployeeOnboarding::where('tenant_id', $tenantId)
                ->with(['employee:id,full_name', 'tasks'])
                ->latest('started_at')
                ->get();

            $this->templates = OnboardingTemplate::where('tenant_id', $tenantId)
                ->withCount('tasks')
                ->get();

            $this->availableEmployees = Employee::where('tenant_id', $tenantId)
                ->whereDoesntHave('onboardings', fn($q) => $q->where('status', 'in_progress'))
                ->get(['id', 'full_name']);
        } else {
            $employee = Employee::where('email', $user->email)
                ->where('tenant_id', $tenantId)
                ->first();

            if ($employee) {
                $this->activeOnboarding = EmployeeOnboarding::where('employee_id', $employee->id)
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'in_progress')
                    ->with('tasks')
                    ->first();
            }
        }
    }

    public function toggleTask($taskId)
    {
        $task = EmployeeOnboardingTask::findOrFail($taskId);
        
        // Security: Ensure owner or admin
        $user = Auth::user();
        if (!$this->isAdmin && $task->onboarding->employee->email !== $user->email) {
            return;
        }

        $task->update([
            'is_completed' => !$task->is_completed,
            'completed_at' => !$task->is_completed ? now() : null
        ]);

        $onboarding = $task->onboarding;
        if ($onboarding->tasks()->where('is_completed', false)->count() === 0) {
            $onboarding->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        } else {
            $onboarding->update(['status' => 'in_progress', 'completed_at' => null]);
        }

        $this->loadData();
        $this->dispatch('notify', message: 'Task status synchronized.', type: 'success');
    }

    public function assignOnboarding()
    {
        $this->validate([
            'selectedEmployeeId' => 'required|exists:employees,id',
            'selectedTemplateId' => 'required|exists:onboarding_templates,id',
        ]);

        $user = Auth::user();
        $tenantId = $user->tenant_id;
        $template = OnboardingTemplate::where('tenant_id', $tenantId)->findOrFail($this->selectedTemplateId);

        $onboarding = EmployeeOnboarding::create([
            'tenant_id' => $tenantId,
            'employee_id' => $this->selectedEmployeeId,
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

        $this->showAssignModal = false;
        $this->reset(['selectedEmployeeId', 'selectedTemplateId']);
        $this->loadData();
        $this->dispatch('notify', message: 'Employee launched into onboarding sequence.', type: 'success');
    }

    public function render()
    {
        return view('livewire.onboarding.onboarding-hub');
    }
}
