<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenants\TenantProvisioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanySignupController extends Controller
{
    public function __construct(private TenantProvisioningService $provisioningService)
    {
    }

    public function create(): View
    {
        return view('auth.company-signup', [
            'countries' => config('geo.countries', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_code' => ['nullable', 'string', 'max:80', 'alpha_dash', Rule::unique('tenants', 'code')],
            'company_email' => ['required', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:3'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'currency' => ['nullable', 'string', 'max:8'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $tenant = null;
        $admin = null;

        DB::transaction(function () use ($validated, &$tenant, &$admin) {
            $baseCode = ! empty($validated['company_code'])
                ? Str::upper($validated['company_code'])
                : Str::upper(Str::slug($validated['company_name'], '_'));
            $code = $this->ensureUniqueCode($baseCode);
            $slug = Str::slug($validated['company_name']);

            $tenant = Tenant::query()->create([
                'name' => $validated['company_name'],
                'code' => $code,
                'slug' => $this->ensureUniqueSlug($slug),
                'email' => $validated['company_email'],
                'phone' => $validated['company_phone'] ?? null,
                'country' => strtoupper((string) ($validated['country'] ?? 'IN')),
                'timezone' => $validated['timezone'] ?? 'Asia/Kolkata',
                'currency' => strtoupper((string) ($validated['currency'] ?? 'INR')),
                'is_active' => true,
                'setup_completed' => false,
            ]);

            $admin = User::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenant->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['password']),
                'is_platform_admin' => false,
            ]);

            $tenant->update(['owner_user_id' => $admin->id]);

            $this->provisioningService->provision($tenant, $admin);
        });

        Auth::login($admin);

        return redirect()->route('onboarding.show')
            ->with('status', 'Company created. Complete setup to start using HRMS.');
    }

    private function ensureUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug ?: 'company';
        $counter = 1;
        while (Tenant::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function ensureUniqueCode(string $baseCode): string
    {
        $code = $baseCode ?: 'COMPANY';
        $counter = 1;
        while (Tenant::query()->where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }

        return $code;
    }
}
