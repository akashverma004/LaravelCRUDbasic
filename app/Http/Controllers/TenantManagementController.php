<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantManagementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'status']);
        $tenants = Tenant::query()
            ->when(! empty($filters['q']), function ($query) use ($filters) {
                $q = $filters['q'];
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($query) => $query->where('is_active', (bool) $filters['status']))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('hrms.tenants.index', compact('tenants', 'filters'));
    }

    public function create(): View
    {
        return view('hrms.tenants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:80', 'alpha_dash', Rule::unique('tenants', 'code')],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('tenants', 'slug')],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'country' => ['nullable', 'string', 'max:3'],
            'timezone' => ['required', 'string', 'max:64'],
            'currency' => ['required', 'string', 'max:8'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        Tenant::query()->create([
            ...$validated,
            'code' => strtoupper($validated['code']),
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'country' => strtoupper((string) ($validated['country'] ?? 'IN')),
            'currency' => strtoupper($validated['currency']),
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'setup_completed' => false,
        ]);

        return redirect()->route('tenants.index')->with('status', 'Tenant created successfully.');
    }

    public function edit(int $tenant): View
    {
        $tenant = Tenant::query()->findOrFail($tenant);

        return view('hrms.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, int $tenant): RedirectResponse
    {
        $tenant = Tenant::query()->findOrFail($tenant);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:80', 'alpha_dash', Rule::unique('tenants', 'code')->ignore($tenant->id)],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('tenants', 'slug')->ignore($tenant->id)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'country' => ['nullable', 'string', 'max:3'],
            'timezone' => ['required', 'string', 'max:64'],
            'currency' => ['required', 'string', 'max:8'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $tenant->update([
            ...$validated,
            'code' => strtoupper($validated['code']),
            'country' => strtoupper((string) ($validated['country'] ?? 'IN')),
            'currency' => strtoupper($validated['currency']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('tenants.index')->with('status', 'Tenant updated successfully.');
    }

    public function destroy(int $tenant): RedirectResponse
    {
        $tenant = Tenant::query()->findOrFail($tenant);
        if ($tenant->code === 'DEFAULT') {
            return redirect()->back()->with('error', 'Default tenant cannot be deleted.');
        }

        $tenant->delete();
        return redirect()->route('tenants.index')->with('status', 'Tenant deleted successfully.');
    }
}
