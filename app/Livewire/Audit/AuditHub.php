<?php

namespace App\Livewire\Audit;

use App\Models\ActivityLog;
use App\Support\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Audit Grid - PeopleFlow HRMS')]
class AuditHub extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $tenantId = TenantContext::id();

        $logs = ActivityLog::where('tenant_id', $tenantId)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->where(function($query) {
                if ($this->search) {
                    $query->where('action', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
                }
            })
            ->paginate(50);

        return view('livewire.audit.audit-hub', [
            'logs' => $logs
        ]);
    }
}
