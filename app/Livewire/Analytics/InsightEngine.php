<?php

namespace App\Livewire\Analytics;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('hrms.layouts.app')]
#[Title('Organizational Insights - PeopleFlow HRMS')]
class InsightEngine extends Component
{
    public $stats = [];
    public $headcountTrend = [];
    public $absenceTrend = [];
    public $attendanceTrend = [];
    public $departmentDistribution = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $tenantId = Auth::user()->tenant_id;

        // 1. Headcount Growth (Last 6 Months)
        $this->headcountTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Employee::where('tenant_id', $tenantId)
                ->where('created_at', '<=', $month->endOfMonth())
                ->count();
            $this->headcountTrend[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        // 2. Absence Trends
        $this->absenceTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $leaveDays = LeaveRequest::where('tenant_id', $tenantId)
                ->where('status', 'approved')
                ->where(function ($query) use ($month) {
                    $query->whereBetween('start_date', [$month->startOfMonth(), $month->endOfMonth()])
                          ->orWhereBetween('end_date', [$month->startOfMonth(), $month->endOfMonth()]);
                })
                ->get()
                ->sum(function ($leave) {
                    return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
                });
            
            $this->absenceTrend[] = [
                'month' => $month->format('M'),
                'days' => $leaveDays
            ];
        }

        // 3. Attendance Heatmap
        $totalEmployees = Employee::where('tenant_id', $tenantId)->count() ?: 1;
        $this->attendanceTrend = [];
        $period = CarbonPeriod::create(now()->subDays(14), now());
        foreach ($period as $date) {
            $count = Attendance::where('tenant_id', $tenantId)
                ->whereDate('punch_in', $date)
                ->distinct('employee_id')
                ->count();
            
            $this->attendanceTrend[] = [
                'day' => $date->format('D d'),
                'percentage' => round(($count / $totalEmployees) * 100)
            ];
        }

        // 4. Department Distribution
        $this->departmentDistribution = Employee::where('tenant_id', $tenantId)
            ->with('department')
            ->get()
            ->groupBy('department_id')
            ->map(fn($group) => [
                'name' => $group->first()->department?->name ?? 'Unassigned',
                'count' => $group->count()
            ])
            ->values()
            ->toArray();

        // 5. High Level Stats
        $this->stats = [
            'totalEmployees' => Employee::where('tenant_id', $tenantId)->count(),
            'activeLeaves' => LeaveRequest::where('tenant_id', $tenantId)->where('status', 'approved')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->count(),
            'presentToday' => Attendance::where('tenant_id', $tenantId)->whereDate('punch_in', now())->distinct('employee_id')->count()
        ];
    }

    public function render()
    {
        return view('livewire.analytics.insight-engine');
    }
}
