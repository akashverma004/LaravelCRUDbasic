<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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

    public function data(): JsonResponse
    {
        $users = User::query()->with('roles')->orderBy('name')->paginate(20);
        $roles = Role::query()->orderBy('name')->get();
        $invitations = TenantInvitation::query()
            ->whereNull('accepted_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'users' => $users->getCollection()->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->values(),
            ])->values(),
            'roles' => $roles->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'label' => ucfirst(str_replace('_', ' ', $role->name)),
            ])->values(),
            'invitations' => $invitations->map(fn (TenantInvitation $invitation) => [
                'id' => $invitation->id,
                'name' => $invitation->name,
                'email' => $invitation->email,
                'role_name' => $invitation->role_name,
                'accept_url' => route('tenant-invitations.accept', $invitation->token),
                'expires_at' => $invitation->expires_at?->format('d M Y, h:i A'),
            ])->values(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
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
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $user->assignRole($validated['role_name']);

        if ($request->expectsJson()) {
            $user->load('roles');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->values(),
                ],
            ]);
        }

        return redirect()->route('tenant-users.index')->with('status', 'User created successfully.');
    }

    public function invite(Request $request): RedirectResponse|JsonResponse
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Invitation created successfully.',
                'invitation' => [
                    'id' => $invitation->id,
                    'name' => $invitation->name,
                    'email' => $invitation->email,
                    'role_name' => $invitation->role_name,
                    'accept_url' => $acceptUrl,
                    'expires_at' => $invitation->expires_at?->format('d M Y, h:i A'),
                ],
            ]);
        }

        return redirect()->route('tenant-users.index')
            ->with('status', 'Invitation created. Share this link: ' . $acceptUrl);
    }
}
