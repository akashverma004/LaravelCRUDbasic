<?php

namespace App\Livewire\Shared;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CommandPalette extends Component
{
    public string $query = '';

    public function render()
    {
        $tenantId = Auth::check() ? Auth::user()->tenant_id : null;
        
        $employees = [];
        if (strlen($this->query) >= 2 && $tenantId) {
            $employees = Employee::where('tenant_id', $tenantId)
                ->where(function($q) {
                    $q->where('first_name', 'like', "%{$this->query}%")
                      ->orWhere('last_name', 'like', "%{$this->query}%")
                      ->orWhere('job_title', 'like', "%{$this->query}%")
                      ->orWhere('email', 'like', "%{$this->query}%");
                })
                ->limit(5)
                ->get();
        }

        return view('livewire.shared.command-palette', [
            'employees' => $employees,
            'staticRoutes' => $this->getStaticRoutes()
        ]);
    }

    private function getStaticRoutes()
    {
        $routes = [
            ['name' => 'Dashboard', 'route' => route('dashboard'), 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['name' => 'Leave Hub', 'route' => route('leaves.my'), 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['name' => 'Payroll Dashboard', 'route' => route('payroll.index'), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['name' => 'Performance 360', 'route' => route('performance.index'), 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['name' => 'Employee Directory', 'route' => route('employees.index'), 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['name' => 'Asset Manager', 'route' => route('assets.index'), 'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10 M1 14l1.224-2.449A2 2 0 014.013 10h6 M13 14h5.618a2 2 0 001.789-1.106l1.5-3A2 2 0 0020.118 8H14 M5 16h3 M13 16h3'],
        ];

        if (empty($this->query)) return $routes;

        return collect($routes)->filter(function($route) {
            return stripos($route['name'], $this->query) !== false;
        })->take(4)->toArray();
    }
}
