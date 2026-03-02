<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Services\AuthorizationService;
use Illuminate\View\View;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct(private AuthorizationService $authorizationService) {}

    public function index(): View
    {
        $this->authorize('manage-roles');
        $users = User::with('roles')->paginate(15);
        return view('hrms.users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        $this->authorize('manage-roles');
        $roles = $this->authorizationService->getAllRoles();
        $userRoles = $user->roles()->pluck('id')->toArray();
        return view('hrms.users.edit-roles', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('manage-roles');

        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()->back()->with('status', 'User roles updated successfully.');
    }

    public function assignRole(Request $request, User $user)
    {
        $this->authorize('manage-roles');

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($validated['role_id']);
        $this->authorizationService->assignRole($user, $role->name);

        return redirect()->back()->with('status', 'Role assigned successfully.');
    }

    public function removeRole(Request $request, User $user)
    {
        $this->authorize('manage-roles');

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($validated['role_id']);
        $this->authorizationService->removeRole($user, $role->name);

        return redirect()->back()->with('status', 'Role removed successfully.');
    }
}
