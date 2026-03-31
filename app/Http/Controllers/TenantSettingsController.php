<?php

namespace App\Http\Controllers;

use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TenantSettingsController extends Controller
{
    public function index(Request $request): View
    {
        $tenant = $request->user()->tenant;
        return view('hrms.settings.index', compact('tenant'));
    }

    public function update(Request $request): RedirectResponse
    {
        $tenant = $request->user()->tenant;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'stamp' => ['nullable', 'image', 'max:2048'],
            'signature' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['name', 'email', 'phone', 'address']);

        if ($request->hasFile('logo')) {
            if ($tenant->logo_path) {
                Storage::disk('public')->delete($tenant->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('tenants/logos', 'public');
        }

        if ($request->hasFile('stamp')) {
            if ($tenant->stamp_path) {
                Storage::disk('public')->delete($tenant->stamp_path);
            }
            $data['stamp_path'] = $request->file('stamp')->store('tenants/stamps', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($tenant->signature_path) {
                Storage::disk('public')->delete($tenant->signature_path);
            }
            $data['signature_path'] = $request->file('signature')->store('tenants/signatures', 'public');
        }

        $tenant->update($data);

        return redirect()->route('settings.index')->with('status', 'Company settings updated successfully.');
    }
}
