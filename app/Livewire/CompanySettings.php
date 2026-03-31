<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanySettings extends Component
{
    use WithFileUploads;

    public $tenant;
    
    // Form fields
    public $name;
    public $email;
    public $phone;
    public $address;

    // File uploads
    public $logo;
    public $signature;
    public $stamp;

    public function mount()
    {
        $this->tenant = Auth::user()->tenant;
        $this->name = $this->tenant->name;
        $this->email = $this->tenant->email;
        $this->phone = $this->tenant->phone;
        $this->address = $this->tenant->address;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
            'stamp' => 'nullable|image|max:2048',
        ]);

        $this->tenant->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        // Handle File Uploads
        if ($this->logo) {
            if ($this->tenant->logo_path) Storage::disk('public')->delete(str_replace('public/', '', $this->tenant->logo_path));
            $path = $this->logo->store('tenants/logos', 'public');
            $this->tenant->update(['logo_path' => 'public/' . $path]);
        }

        if ($this->signature) {
            if ($this->tenant->signature_path) Storage::disk('public')->delete(str_replace('public/', '', $this->tenant->signature_path));
            $path = $this->signature->store('tenants/signatures', 'public');
            $this->tenant->update(['signature_path' => 'public/' . $path]);
        }

        if ($this->stamp) {
            if ($this->tenant->stamp_path) Storage::disk('public')->delete(str_replace('public/', '', $this->tenant->stamp_path));
            $path = $this->stamp->store('tenants/stamps', 'public');
            $this->tenant->update(['stamp_path' => 'public/' . $path]);
        }

        session()->flash('status', 'Company settings updated successfully.');
        
        // Reset file inputs
        $this->logo = null;
        $this->signature = null;
        $this->stamp = null;
    }

    public function render()
    {
        return view('livewire.company-settings')->layout('hrms.layouts.app');
    }
}
