<?php

namespace App\Livewire\Auth;

use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Join Workspace - PeopleFlow HRMS')]
class AcceptInvitation extends Component
{
    public TenantInvitation $invitation;
    public string $name = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token)
    {
        $this->invitation = TenantInvitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();
            
        $this->name = $this->invitation->name;
    }

    public function accept()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'tenant_id' => $this->invitation->tenant_id,
            'name' => $this->name,
            'email' => $this->invitation->email,
            'password' => Hash::make($this->password),
            'role' => $this->invitation->role_name,
        ]);

        $user->assignRole($this->invitation->role_name);

        $this->invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        auth()->login($user);

        return redirect(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.accept-invitation');
    }
}
