<?php

namespace App\Livewire\Auth;

use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenants\TenantProvisioningService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Create Account - PeopleFlow HRMS')]
class CompanySignup extends Component
{
    // Company Details
    public string $company_name = '';
    public string $company_code = '';
    public string $company_email = '';
    public string $country = 'IN';

    // Admin Details
    public string $admin_name = '';
    public string $admin_email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function signup(TenantProvisioningService $provisioningService)
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'nullable|string|max:80|unique:tenants,code',
            'company_email' => 'required|email|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = null;

        DB::transaction(function () use ($provisioningService, &$admin) {
            $baseCode = ! empty($this->company_code)
                ? Str::upper($this->company_code)
                : Str::upper(Str::slug($this->company_name, '_'));
            
            $code = $this->ensureUniqueCode($baseCode);
            $slug = Str::slug($this->company_name);

            $tenant = Tenant::create([
                'name' => $this->company_name,
                'code' => $code,
                'slug' => $this->ensureUniqueSlug($slug),
                'email' => $this->company_email,
                'country' => strtoupper($this->country),
                'timezone' => 'Asia/Kolkata',
                'currency' => 'INR',
                'is_active' => true,
                'setup_completed' => false,
            ]);

            $admin = User::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenant->id,
                'name' => $this->admin_name,
                'email' => $this->admin_email,
                'password' => Hash::make($this->password),
                'is_platform_admin' => false,
            ]);

            $tenant->update(['owner_user_id' => $admin->id]);

            $provisioningService->provision($tenant, $admin);
        });

        Auth::login($admin);

        return redirect(route('onboarding.show'))
            ->with('status', 'Company created. Complete setup to start using HRMS.');
    }

    private function ensureUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug ?: 'company';
        $counter = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    private function ensureUniqueCode(string $baseCode): string
    {
        $code = $baseCode ?: 'COMPANY';
        $counter = 1;
        while (Tenant::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }
        return $code;
    }

    public function render()
    {
        $countries = config('geo.countries', [
            'IN' => 'India',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'CA' => 'Canada',
            'AU' => 'Australia',
        ]);

        return view('livewire.auth.company-signup', [
            'countries' => $countries
        ]);
    }
}
