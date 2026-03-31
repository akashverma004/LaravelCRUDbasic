<?php

namespace App\Livewire\Platform;

use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Workspaces - PeopleFlow Platform')]
class Workspaces extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    // Workspace Form State
    public $showModal = false;
    public $editingTenantId;
    public $name = '';
    public $code = '';
    public $slug = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $country = 'IN';
    public $timezone = 'UTC';
    public $currency = 'INR';
    public $isActive = true;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:80', 'alpha_dash', Rule::unique('tenants', 'code')->ignore($this->editingTenantId)],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('tenants', 'slug')->ignore($this->editingTenantId)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'country' => ['nullable', 'string', 'max:3'],
            'timezone' => ['required', 'string', 'max:64'],
            'currency' => ['required', 'string', 'max:8'],
            'isActive' => ['boolean'],
        ];
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->editingTenantId = $id;

        if ($id) {
            $tenant = Tenant::findOrFail($id);
            $this->name = $tenant->name;
            $this->code = $tenant->code;
            $this->slug = $tenant->slug;
            $this->email = $tenant->email;
            $this->phone = $tenant->phone;
            $this->address = $tenant->address;
            $this->country = $tenant->country;
            $this->timezone = $tenant->timezone;
            $this->currency = $tenant->currency;
            $this->isActive = (bool) $tenant->is_active;
        } else {
            $this->reset(['name', 'code', 'slug', 'email', 'phone', 'address', 'country', 'timezone', 'currency', 'isActive']);
            $this->country = 'IN';
            $this->timezone = 'UTC';
            $this->currency = 'INR';
            $this->isActive = true;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'slug' => $this->slug ?: Str::slug($this->name),
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'country' => strtoupper($this->country),
            'timezone' => $this->timezone,
            'currency' => strtoupper($this->currency),
            'is_active' => $this->isActive,
        ];

        if ($this->editingTenantId) {
            Tenant::findOrFail($this->editingTenantId)->update($data);
            $this->dispatch('notify', message: 'Workspace configuration synchronized.', type: 'success');
        } else {
            Tenant::create([
                ...$data,
                'setup_completed' => false,
            ]);
            $this->dispatch('notify', message: 'New organizational sector initialized.', type: 'success');
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        $tenant = Tenant::findOrFail($id);
        if ($tenant->code === 'DEFAULT') {
            $this->dispatch('notify', message: 'The primary workspace node cannot be detached.', type: 'error');
            return;
        }
        $tenant->delete();
        $this->dispatch('notify', message: 'Workspace purged from local sector.', type: 'warning');
    }

    public function render()
    {
        $tenants = Tenant::query()
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('is_active', (bool) $this->status))
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.platform.workspaces', [
            'tenants' => $tenants
        ]);
    }
}
