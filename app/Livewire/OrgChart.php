<?php

namespace App\Livewire;

use App\Services\OrganizationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('hrms.layouts.app')]
#[Title('Organization Chart - PeopleFlow HRMS')]
class OrgChart extends Component
{
    public $ceo;
    public $stats;

    public function mount(OrganizationService $organizationService)
    {
        $this->ceo = $organizationService->getOrganizationHierarchy()->first();
        $this->stats = $organizationService->getOrgChartStats();
    }

    public function render()
    {
        return view('livewire.org-chart');
    }
}
