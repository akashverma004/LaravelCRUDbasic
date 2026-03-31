<?php

namespace App\Livewire\Lattice;

use App\Models\Role;
use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('User Lattice - PeopleFlow HRMS')]
class UserStation extends Component
{
    use WithPagination;

    public $search = '';
    
    // User Form
    public $name = '';
    public $email = '';
    public $password = '';
    public $roleName = 'employee';
    public bool $showUserModal = false;

    // Invitation Form
    public $inviteName = '';
    public $inviteEmail = '';
    public $inviteRoleName = 'employee';
    public bool $showInviteModal = false;

    public $selectedUserId;
    public array $userRoles = [];
    public bool $showRoleModal = false;

    public function updatingSearch() { $this->resetPage(); }

    public function openRoleModal($id)
    {
        $this->selectedUserId = $id;
        $user = User::with('roles')->findOrFail($id);
        $this->userRoles = $user->roles->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->showRoleModal = true;
    }

    public function saveUserRoles()
    {
        abort_unless(Auth::user()->hasAnyRole(['admin', 'hr_manager']), 403);
        $user = User::findOrFail($this->selectedUserId);
        $user->roles()->sync(array_map('intval', $this->userRoles));
        $this->showRoleModal = false;
        $this->dispatch('notify', message: 'Identity privileges recalibrated.', type: 'success');
    }

    public function createUser()
    {
        abort_unless(Auth::user()->hasAnyRole(['admin', 'hr_manager']), 403);
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->where(fn ($q) => $q->where('tenant_id', Auth::user()->tenant_id))],
            'password' => ['required', 'min:8'],
            'roleName' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_platform_admin' => false,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        $user->assignRole($this->roleName);

        $this->reset(['name', 'email', 'password', 'roleName', 'showUserModal']);
        $this->dispatch('notify', message: 'User provisioned in lattice.', type: 'success');
    }

    public function inviteUser()
    {
        abort_unless(Auth::user()->hasAnyRole(['admin', 'hr_manager']), 403);
        $this->validate([
            'inviteName' => ['nullable', 'string', 'max:255'],
            'inviteEmail' => ['required', 'email', 'max:255'],
            'inviteRoleName' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        $invitation = TenantInvitation::create([
            'name' => $this->inviteName ?: null,
            'email' => $this->inviteEmail,
            'role_name' => $this->inviteRoleName,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
            'invited_by' => Auth::id(),
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        $this->reset(['inviteName', 'inviteEmail', 'inviteRoleName', 'showInviteModal']);
        $this->dispatch('notify', message: 'Invitation link generated.', type: 'success');
    }

    public function deleteInvitation($id)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin', 'hr_manager']), 403);
        TenantInvitation::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Invitation revoked.', type: 'success');
    }

    public function render()
    {
        $users = User::query()
            ->where('tenant_id', Auth::user()->tenant_id)
            ->with('roles')
            ->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->orderBy('name')
            ->paginate(12);

        $roles = Role::where(function($q) {
            $q->where('tenant_id', Auth::user()->tenant_id)->orWhereNull('tenant_id');
        })->orderBy('name')->get();
        
        $invitations = TenantInvitation::where('tenant_id', Auth::user()->tenant_id)
            ->whereNull('accepted_at')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.lattice.user-station', [
            'users' => $users,
            'roles' => $roles,
            'invitations' => $invitations
        ]);
    }
}
