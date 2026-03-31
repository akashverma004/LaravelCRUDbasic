<?php

namespace App\Livewire\Policies;

use App\Models\HolidayPolicy;
use App\Models\HolidayPolicyDate;
use App\Support\GeoLookup;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Holiday Governance - PeopleFlow HRMS')]
class HolidayHub extends Component
{
    use WithPagination;

    public $activeTab = 'policies'; // policies, calendar
    public $search = '';

    // Policy Form
    public $showPolicyModal = false;
    public $editingPolicyId;
    public $name = '';
    public $code = '';
    public $description = '';
    public $countryCode = 'IN';
    public $stateCode = 'DL';
    public array $weekendDays = ['saturday', 'sunday'];
    public $isActive = true;

    // Date/Calendar state
    public $selectedPolicyId;
    public $showDateModal = false;
    public $editingDateId;
    public $dateName = '';
    public $holidayDate = '';
    public $isOptional = false;

    public function mount()
    {
        $this->selectedPolicyId = HolidayPolicy::first()?->id;
    }

    public function updatingSearch() { $this->resetPage(); }

    // --- Policy Actions ---

    public function openPolicyModal($id = null)
    {
        $this->resetValidation();
        $this->editingPolicyId = $id;

        if ($id) {
            $policy = HolidayPolicy::findOrFail($id);
            $this->name = $policy->name;
            $this->code = $policy->code;
            $this->description = $policy->description;
            $this->countryCode = $policy->country_code;
            $this->stateCode = $policy->state_code;
            $this->weekendDays = $policy->weekend_days ?? [];
            $this->isActive = (bool) $policy->is_active;
        } else {
            $this->reset(['name', 'code', 'description', 'isActive']);
            $this->countryCode = 'IN';
            $this->stateCode = 'DL';
            $this->weekendDays = ['saturday', 'sunday'];
            $this->isActive = true;
        }

        $this->showPolicyModal = true;
    }

    public function savePolicy()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'countryCode' => 'required',
            'stateCode' => 'required',
        ]);

        $data = [
            'name' => $this->name,
            'code' => $this->code ?: strtoupper('HOLIDAY_' . $this->countryCode . '_' . $this->stateCode . '_' . Str::random(4)),
            'description' => $this->description,
            'country_code' => strtoupper($this->countryCode),
            'state_code' => strtoupper($this->stateCode),
            'weekend_days' => $this->weekendDays,
            'is_active' => $this->isActive,
        ];

        if ($this->editingPolicyId) {
            HolidayPolicy::findOrFail($this->editingPolicyId)->update($data);
            $this->dispatch('notify', message: 'Holiday policy recalibrated.', type: 'success');
        } else {
            HolidayPolicy::create($data);
            $this->dispatch('notify', message: 'New holiday jurisdictional node provisioned.', type: 'success');
        }

        $this->showPolicyModal = false;
    }

    public function deletePolicy($id)
    {
        HolidayPolicy::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Policy purged from system.', type: 'warning');
    }

    // --- Date Actions ---

    public function openDateModal($id = null)
    {
        if (!$this->selectedPolicyId) return;
        
        $this->resetValidation();
        $this->editingDateId = $id;

        if ($id) {
            $date = HolidayPolicyDate::findOrFail($id);
            $this->dateName = $date->name;
            $this->holidayDate = $date->holiday_date->format('Y-m-d');
            $this->isOptional = (bool) $date->is_optional;
        } else {
            $this->reset(['dateName', 'holidayDate', 'isOptional']);
        }

        $this->showDateModal = true;
    }

    public function saveDate()
    {
        $this->validate([
            'dateName' => 'required',
            'holidayDate' => 'required|date',
        ]);

        $data = [
            'holiday_policy_id' => $this->selectedPolicyId,
            'name' => $this->dateName,
            'holiday_date' => $this->holidayDate,
            'is_optional' => $this->isOptional,
            'rules' => [],
        ];

        if ($this->editingDateId) {
            HolidayPolicyDate::findOrFail($this->editingDateId)->update($data);
            $this->dispatch('notify', message: 'Calendar event synchronized.', type: 'success');
        } else {
            HolidayPolicyDate::create($data);
            $this->dispatch('notify', message: 'Temporal signal injected into calendar.', type: 'success');
        }

        $this->showDateModal = false;
    }

    public function deleteDate($id)
    {
        HolidayPolicyDate::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Calendar entry cleared.', type: 'warning');
    }

    public function render()
    {
        $policies = HolidayPolicy::query()
            ->withCount('holidayDates')
            ->where('name', 'like', "%{$this->search}%")
            ->orderBy('name')
            ->paginate(12);

        $selectedPolicy = $this->selectedPolicyId 
            ? HolidayPolicy::with(['holidayDates' => fn($q) => $q->orderBy('holiday_date')])->find($this->selectedPolicyId)
            : null;

        return view('livewire.policies.holiday-hub', [
            'policies' => $policies,
            'selectedPolicy' => $selectedPolicy,
            'countries' => config('geo.countries', []),
            'states' => config('geo.states_in', []),
        ]);
    }
}
