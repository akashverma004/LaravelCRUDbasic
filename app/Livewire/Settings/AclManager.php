<?php

namespace App\Livewire\Settings;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\AuthorizationService;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Access Control - PeopleFlow HRMS')]
class AclManager extends Component
{
    use WithPagination;

    public $activeTab = 'roles'; // roles, users
    public $search = '';

    // Role Form
    public $showRoleModal = false;
    public $editingRoleId;
    public $roleName = '';
    public $roleDisplayName = '';
    public $roleDescription = '';
    public array $selectedPermissions = [];

    // User Role Form
    public $showUserRoleModal = false;
    public $selectedUserId;
    public array $userRoles = [];

    protected $listeners = ['refresh' => '$refresh'];

    public function mount()
    {
        Gate::authorize('manage-roles');
    }

    public function updatingSearch() { $this->resetPage(); }

    // --- Role Actions ---

    public function openRoleModal($id = null)
    {
        $this->reset(['roleName', 'roleDisplayName', 'roleDescription', 'selectedPermissions', 'editingRoleId']);
        if ($id) {
            $role = Role::with('permissions')->findOrFail($id);
            $this->editingRoleId = $role->id;
            $this->roleName = $role->name;
            $this->roleDisplayName = $role->display_name;
            $this->roleDescription = $role->description;
            $this->selectedPermissions = $role->permissions->pluck('id')->map(fn($id) => (string)$id)->toArray();
        }
        $this->showRoleModal = true;
    }

    public function saveRole(AuthorizationService $authService)
    {
        $this->validate([
            'roleName' => 'required|alpha_dash',
            'roleDisplayName' => 'required',
        ]);

        $data = [
            'name' => $this->roleName,
            'display_name' => $this->roleDisplayName,
            'description' => $this->roleDescription,
            'permissions' => array_map('intval', $this->selectedPermissions),
        ];

        if ($this->editingRoleId) {
            $role = Role::findOrFail($this->editingRoleId);
            $authService->updateRole($role, $data);
            $msg = 'Role updated in security grid.';
        } else {
            $authService->createRole($data);
            $msg = 'New role provisioned.';
        }

        $this->showRoleModal = false;
        $this->dispatch('notify', message: $msg, type: 'success');
    }

    public function deleteRole($id, AuthorizationService $authService)
    {
        $role = Role::findOrFail($id);
        if ($role->users()->exists()) {
            $this->dispatch('notify', message: 'Cannot delete role with assigned users.', type: 'error');
            return;
        }
        $authService->deleteRole($role);
        $this->dispatch('notify', message: 'Role purged from system.', type: 'warning');
    }

    // --- User Actions ---

    public function openUserRoleModal($id)
    {
        $this->selectedUserId = $id;
        $user = User::with('roles')->findOrFail($id);
        $this->userRoles = $user->roles->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->showUserRoleModal = true;
    }

    public function saveUserRoles()
    {
        $user = User::findOrFail($this->selectedUserId);
        $user->roles()->sync(array_map('intval', $this->userRoles));
        $this->showUserRoleModal = false;
        $this->dispatch('notify', message: 'Identity privileges recalibrated.', type: 'success');
    }

    public function render(AuthorizationService $authService)
    {
        $roles = $authService->getAllRoles();
        $permissionsByModule = $authService->getAllPermissions()->groupBy('module');

        $users = User::query()
            ->with('roles')
            ->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->paginate(12);

        return view('livewire.settings.acl-manager', [
            'roles' => $roles,
            'permissionsByModule' => $permissionsByModule,
            'users' => $users,
        ]);
    }
}
