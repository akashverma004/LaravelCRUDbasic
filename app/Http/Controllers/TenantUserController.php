<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->with('roles')->orderBy('name')->paginate(20);
        $roles = Role::query()->orderBy('name')->get();
        $invitations = TenantInvitation::query()
            ->whereNull('accepted_at')
            ->orderByDesc('created_at')
            ->get();

        return view('hrms.tenant-users.index', compact('users', 'roles', 'invitations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->where(fn ($q) => $q->where('tenant_id', auth()->user()->tenant_id))],
            'password' => ['required', 'min:8'],
            'role_name' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_platform_admin' => false,
        ]);

        $user->assignRole($validated['role_name']);

        return redirect()->route('tenant-users.index')->with('status', 'User created successfully.');
    }

    public function invite(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role_name' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        $invitation = TenantInvitation::query()->create([
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'],
            'role_name' => $validated['role_name'],
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
            'invited_by' => auth()->id(),
        ]);

        $acceptUrl = route('tenant-invitations.accept', ['token' => $invitation->token]);

        return redirect()->route('tenant-users.index')
            ->with('status', 'Invitation created. Share this link: ' . $acceptUrl);
    }
}
