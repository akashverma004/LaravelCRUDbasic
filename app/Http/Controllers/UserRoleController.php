<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Services\AuthorizationService;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    public function __construct(private AuthorizationService $authorizationService) {}

    public function index(): View
    {
        $this->authorize('manage-roles');
        $users = User::with('roles')->paginate(15);
        $roles = $this->authorizationService->getAllRoles();
        return view('hrms.users.index', compact('users', 'roles'));
    }

    public function edit(int $user): RedirectResponse
    {
        $this->authorize('manage-roles');
        return redirect()->route('users.index');
    }

    public function update(Request $request, int $user): RedirectResponse|JsonResponse
    {
        $this->authorize('manage-roles');
        $user = User::query()->findOrFail($user);
        $tenantId = TenantContext::id() ?? $user->tenant_id;

        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => [
                'integer',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User roles updated successfully.',
                'user' => $this->transformUser($user->fresh('roles')),
            ]);
        }

        return redirect()->back()->with('status', 'User roles updated successfully.');
    }

    public function assignRole(Request $request, int $user): RedirectResponse|JsonResponse
    {
        $this->authorize('manage-roles');
        $user = User::query()->findOrFail($user);
        $tenantId = TenantContext::id() ?? $user->tenant_id;

        $validated = $request->validate([
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
        ]);

        $role = Role::find($validated['role_id']);
        $this->authorizationService->assignRole($user, $role->name);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role assigned successfully.',
                'user' => $this->transformUser($user->fresh('roles')),
            ]);
        }

        return redirect()->back()->with('status', 'Role assigned successfully.');
    }

    public function removeRole(Request $request, int $user): RedirectResponse|JsonResponse
    {
        $this->authorize('manage-roles');
        $user = User::query()->findOrFail($user);
        $tenantId = TenantContext::id() ?? $user->tenant_id;

        $validated = $request->validate([
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
        ]);

        $role = Role::find($validated['role_id']);
        $this->authorizationService->removeRole($user, $role->name);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role removed successfully.',
                'user' => $this->transformUser($user->fresh('roles')),
            ]);
        }

        return redirect()->back()->with('status', 'Role removed successfully.');
    }

    private function transformUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->map(fn (Role $role) => [
                'id' => $role->id,
                'display_name' => $role->display_name,
                'name' => $role->name,
            ])->values(),
            'update_url' => route('users.update-roles', $user),
            'assign_url' => route('users.assign-role', $user),
            'remove_url' => route('users.remove-role', $user),
        ];
    }
}
