<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\TenantInvitation;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TenantInvitationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = TenantInvitation::withoutGlobalScope('tenant')
            ->where('token', $token)
            ->firstOrFail();

        abort_if($invitation->accepted_at !== null, 410, 'This invitation is already used.');
        abort_if($invitation->expires_at && $invitation->expires_at->isPast(), 410, 'This invitation has expired.');

        return view('auth.accept-invitation', compact('invitation'));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $invitation = TenantInvitation::withoutGlobalScope('tenant')
            ->where('token', $token)
            ->firstOrFail();

        abort_if($invitation->accepted_at !== null, 410, 'This invitation is already used.');
        abort_if($invitation->expires_at && $invitation->expires_at->isPast(), 410, 'This invitation has expired.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $previousTenantId = TenantContext::id();
        TenantContext::set((int) $invitation->tenant_id);

        try {
            $user = User::withoutGlobalScope('tenant')
                ->where('tenant_id', $invitation->tenant_id)
                ->where('email', $invitation->email)
                ->first();

            if (! $user) {
                $user = User::withoutGlobalScope('tenant')->create([
                    'tenant_id' => $invitation->tenant_id,
                    'name' => $validated['name'],
                    'email' => $invitation->email,
                    'password' => Hash::make($validated['password']),
                    'is_platform_admin' => false,
                ]);
            } else {
                $user->update([
                    'name' => $validated['name'],
                    'password' => Hash::make($validated['password']),
                ]);
            }

            $role = Role::query()->where('name', $invitation->role_name)->first();
            if ($role) {
                $user->assignRole($role);
            }

            $invitation->update(['accepted_at' => now()]);
        } finally {
            TenantContext::set($previousTenantId);
        }

        Auth::login($user);
        return redirect()->route('dashboard')->with('status', 'Invitation accepted. Welcome to your company workspace.');
    }
}
