<?php

namespace App\Livewire\Performance;

use App\Models\Employee;
use App\Models\Goal;
use App\Models\OneOnOneNote;
use App\Models\PerformanceReview;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Performance Hub - PeopleFlow HRMS')]
class PerformanceHub extends Component
{
    use WithPagination;

    public string $activeTab = 'goals'; // goals, reviews, meetings
    public bool $isManager = false;
    public ?Employee $employee = null;

    // Modals
    public bool $showGoalModal = false;
    public bool $showReviewModal = false;
    public bool $showMeetingModal = false;

    // Form fields (Goal)
    public string $goalTitle = '';
    public string $goalDescription = '';
    public string $goalDueDate = '';
    public string $goalPriority = 'medium';
    public ?int $goalEmployeeId = null;

    // Form fields (Review)
    public ?int $reviewEmployeeId = null;
    public string $reviewCycle = '';
    public int $reviewRating = 3;
    public string $reviewFeedback = '';

    // Form fields (Meeting Note)
    public ?int $meetingEmployeeId = null;
    public string $meetingDate = '';
    public string $meetingTalkingPoints = '';

    public function mount()
    {
        $this->isManager = Auth::user()->hasAnyRole(['admin', 'hr_manager']);
        $this->employee = Employee::where('email', Auth::user()->email)->where('tenant_id', Auth::user()->tenant_id)->first();
        $this->meetingDate = now()->format('Y-m-d');
        $this->goalDueDate = now()->addMonths(3)->format('Y-m-d');
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function saveGoal()
    {
        $this->validate([
            'goalTitle' => 'required|max:255',
            'goalPriority' => 'required',
            'goalEmployeeId' => $this->isManager ? 'required|exists:employees,id' : 'nullable',
        ]);

        Goal::create([
            'tenant_id' => Auth::user()->tenant_id,
            'employee_id' => $this->isManager ? $this->goalEmployeeId : $this->employee?->id,
            'title' => $this->goalTitle,
            'description' => $this->goalDescription,
            'due_date' => $this->goalDueDate,
            'priority' => $this->goalPriority,
            'status' => 'active',
        ]);

        $this->showGoalModal = false;
        $this->reset(['goalTitle', 'goalDescription']);
        $this->dispatch('notify', message: 'Strategic goal objective defined.', type: 'success');
    }

    public function updateGoalProgress(int $id, int $progress)
    {
        $goal = Goal::find($id);
        if ($goal && ($this->isManager || $goal->employee_id == $this->employee?->id)) {
            $goal->update([
                'progress' => $progress,
                'status' => $progress >= 100 ? 'completed' : 'active',
            ]);
            $this->dispatch('notify', message: 'Goal progress synchronized.', type: 'info');
        }
    }

    public function saveReview()
    {
        if (!$this->isManager) abort(403);

        $this->validate([
            'reviewEmployeeId' => 'required|exists:employees,id',
            'reviewCycle' => 'required|max:100',
            'reviewRating' => 'required|integer|min:1|max:5',
            'reviewFeedback' => 'required',
        ]);

        PerformanceReview::create([
            'tenant_id' => Auth::user()->tenant_id,
            'employee_id' => $this->reviewEmployeeId,
            'reviewer_id' => Auth::id(),
            'review_cycle' => $this->reviewCycle,
            'rating' => $this->reviewRating,
            'feedback' => $this->reviewFeedback,
            'status' => 'submitted',
        ]);

        $this->showReviewModal = false;
        $this->reset(['reviewCycle', 'reviewFeedback', 'reviewRating']);
        $this->dispatch('notify', message: 'Performance evaluation submitted.', type: 'success');
    }

    public function saveMeeting()
    {
        $this->validate([
            'meetingEmployeeId' => 'required|exists:employees,id',
            'meetingDate' => 'required|date',
            'meetingTalkingPoints' => 'required',
        ]);

        OneOnOneNote::create([
            'tenant_id' => Auth::user()->tenant_id,
            'manager_id' => Auth::id(),
            'employee_id' => $this->meetingEmployeeId,
            'meeting_date' => $this->meetingDate,
            'talking_points' => $this->meetingTalkingPoints,
        ]);

        $this->showMeetingModal = false;
        $this->reset(['meetingTalkingPoints']);
        $this->dispatch('notify', message: '1-on-1 meeting session documented.', type: 'success');
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;
        
        $goals = Goal::where('tenant_id', $tenantId)
            ->with('employee')
            ->when(!$this->isManager, fn($q) => $q->where('employee_id', $this->employee?->id ?? 0))
            ->orderByDesc('created_at')
            ->paginate(10, pageName: 'goals-page');

        $reviews = PerformanceReview::where('tenant_id', $tenantId)
            ->with(['reviewer', 'employee'])
            ->when(!$this->isManager, fn($q) => $q->where('employee_id', $this->employee?->id ?? 0))
            ->orderByDesc('created_at')
            ->paginate(10, pageName: 'reviews-page');

        $meetings = OneOnOneNote::where('tenant_id', $tenantId)
            ->with(['manager', 'employee'])
            ->when(!$this->isManager, fn($q) => $q->where('employee_id', $this->employee?->id ?? 0))
            ->orderByDesc('meeting_date')
            ->paginate(10, pageName: 'meetings-page');

        $employees = $this->isManager 
            ? Employee::where('tenant_id', $tenantId)->get(['id', 'full_name']) 
            : collect();

        return view('livewire.performance.performance-hub', [
            'goals' => $goals,
            'reviews' => $reviews,
            'meetings' => $meetings,
            'employees' => $employees,
        ]);
    }
}
