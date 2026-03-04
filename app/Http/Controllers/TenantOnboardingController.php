<?php

namespace App\Http\Controllers;

use App\Models\LeavePolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantOnboardingController extends Controller
{
    public function show(Request $request): View
    {
        $tenant = $request->user()->tenant;
        $leavePolicy = LeavePolicy::query()->first();

        return view('hrms.onboarding.setup', [
            'tenant' => $tenant,
            'leavePolicy' => $leavePolicy,
            'countries' => config('geo.countries', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $request->user()->tenant;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'country' => ['nullable', 'string', 'max:3'],
            'timezone' => ['required', 'string', 'max:64'],
            'currency' => ['required', 'string', 'max:8'],
            'annual_limit' => ['required', 'integer', 'min:0'],
            'sick_limit' => ['required', 'integer', 'min:0'],
            'casual_limit' => ['required', 'integer', 'min:0'],
            'unpaid_limit' => ['required', 'integer', 'min:0'],
        ]);

        $tenant->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'country' => strtoupper((string) ($validated['country'] ?? 'IN')),
            'timezone' => $validated['timezone'],
            'currency' => strtoupper($validated['currency']),
            'setup_completed' => true,
            'setup_completed_at' => now(),
        ]);

        $policy = LeavePolicy::query()->first();
        if ($policy) {
            $policy->update([
                'annual_limit' => $validated['annual_limit'],
                'sick_limit' => $validated['sick_limit'],
                'casual_limit' => $validated['casual_limit'],
                'unpaid_limit' => $validated['unpaid_limit'],
            ]);
        }

        return redirect()->route('dashboard')
            ->with('status', 'Company setup completed successfully.');
    }
}
