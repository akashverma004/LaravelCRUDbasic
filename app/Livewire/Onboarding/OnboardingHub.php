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

    // Template Builder Form
    public bool $templateBuilderMode = false;
    public $builderId = null;
    public $builderName = '';
    public $builderDescription = '';
    public $builderTasks = [];

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

    // --- Template Builder Logic ---

    public function openTemplateBuilder($templateId = null)
    {
        $this->templateBuilderMode = true;
        if ($templateId) {
            $template = OnboardingTemplate::where('tenant_id', Auth::user()->tenant_id)->with('tasks')->findOrFail($templateId);
            $this->builderId = $template->id;
            $this->builderName = $template->name;
            $this->builderDescription = $template->description;
            $this->builderTasks = $template->tasks->map(fn($t) => ['title' => $t->title, 'description' => $t->description])->toArray();
        } else {
            $this->builderId = null;
            $this->builderName = '';
            $this->builderDescription = '';
            $this->builderTasks = [['title' => '', 'description' => '']];
        }
    }

    public function addBuilderTask()
    {
        $this->builderTasks[] = ['title' => '', 'description' => ''];
    }

    public function removeBuilderTask($index)
    {
        unset($this->builderTasks[$index]);
        $this->builderTasks = array_values($this->builderTasks); // Re-index array
    }

    public function saveTemplate()
    {
        $this->validate([
            'builderName' => 'required|string|max:255',
            'builderDescription' => 'nullable|string',
            'builderTasks' => 'required|array|min:1',
            'builderTasks.*.title' => 'required|string|max:255',
            'builderTasks.*.description' => 'nullable|string',
        ]);

        $tenantId = Auth::user()->tenant_id;
        
        $template = OnboardingTemplate::updateOrCreate(
            ['id' => $this->builderId, 'tenant_id' => $tenantId],
            ['name' => $this->builderName, 'description' => $this->builderDescription]
        );

        // Nuke old tasks and rebuild to preserve sort_order natively
        \App\Models\OnboardingTemplateTask::where('template_id', $template->id)->delete();

        foreach ($this->builderTasks as $idx => $taskData) {
            \App\Models\OnboardingTemplateTask::create([
                'template_id' => $template->id,
                'title' => $taskData['title'],
                'description' => $taskData['description'] ?? '',
                'sort_order' => $idx
            ]);
        }

        $this->closeTemplateBuilder();
        $this->dispatch('notify', message: 'Blueprint generated and saved to the matrix.', type: 'success');
        $this->loadData();
    }

    public function deleteTemplate($templateId)
    {
        OnboardingTemplate::where('tenant_id', Auth::user()->tenant_id)->findOrFail($templateId)->delete();
        $this->loadData();
        $this->dispatch('notify', message: 'Blueprint wiped from storage.', type: 'info');
    }

    public function closeTemplateBuilder()
    {
        $this->templateBuilderMode = false;
        $this->builderId = null;
        $this->builderName = '';
        $this->builderDescription = '';
        $this->builderTasks = [];
    }

    public function render()
    {
        return view('livewire.onboarding.onboarding-hub');
    }
}
