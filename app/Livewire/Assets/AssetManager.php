<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Employee;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Asset Inventory - PeopleFlow HRMS')]
class AssetManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showEditModal = false;
    public bool $isAdmin = false;

    // Form fields
    public ?int $selectedAssetId = null;
    public string $name = '';
    public string $category = '';
    public string $serial_number = '';
    public ?int $employee_id = null;
    public string $status = 'available';
    public string $notes = '';

    protected $queryString = ['search' => ['except' => '']];

    public function mount()
    {
        $this->isAdmin = Auth::user()->hasAnyRole(['admin', 'hr_manager']);
    }

    public function openCreateModal()
    {
        $this->reset(['selectedAssetId', 'name', 'category', 'serial_number', 'employee_id', 'status', 'notes']);
        $this->showEditModal = true;
    }

    public function openEditModal(int $id)
    {
        $asset = Asset::findOrFail($id);
        $this->selectedAssetId = $asset->id;
        $this->name = $asset->name;
        $this->category = $asset->category;
        $this->serial_number = $asset->serial_number ?? '';
        $this->employee_id = $asset->employee_id;
        $this->status = $asset->status;
        $this->notes = $asset->notes ?? '';
        $this->showEditModal = true;
    }

    public function save()
    {
        if (!$this->isAdmin) abort(403);

        $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'serial_number' => 'nullable|string|max:100',
            'employee_id' => 'nullable|exists:employees,id',
            'status' => 'required',
            'notes' => 'nullable|string',
        ]);

        Asset::updateOrCreate(
            ['id' => $this->selectedAssetId],
            [
                'tenant_id' => Auth::user()->tenant_id,
                'name' => $this->name,
                'category' => $this->category,
                'serial_number' => $this->serial_number,
                'employee_id' => $this->employee_id,
                'status' => $this->status,
                'notes' => $this->notes,
                'assigned_at' => $this->employee_id ? ($this->selectedAssetId ? Asset::find($this->selectedAssetId)->assigned_at ?? now() : now()) : null,
            ]
        );

        $this->showEditModal = false;
        $this->dispatch('notify', message: 'Asset inventory updated.', type: 'success');
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;
        $query = Asset::where('tenant_id', $tenantId)
            ->with('employee')
            ->when($this->search, function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('serial_number', 'like', "%{$this->search}%")
                  ->orWhereHas('employee', fn($e) => $e->where('full_name', 'like', "%{$this->search}%"));
            });

        if (!$this->isAdmin) {
            $employee = Employee::where('email', Auth::user()->email)->where('tenant_id', $tenantId)->first();
            $query->where('employee_id', $employee?->id ?? 0);
        }

        $assets = $query->orderByDesc('created_at')->paginate(12);
        
        $employees = $this->isAdmin 
            ? Employee::where('tenant_id', $tenantId)->get(['id', 'full_name']) 
            : collect();

        return view('livewire.assets.asset-manager', [
            'assets' => $assets,
            'employees' => $employees,
            'categories' => Asset::categories(),
            'statuses' => Asset::statuses(),
        ]);
    }
}
