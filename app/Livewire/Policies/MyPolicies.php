<?php

namespace App\Livewire\Policies;

use App\Models\Employee;
use App\Support\PolicyDefinitions;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('hrms.layouts.app')]
#[Title('Corporate Policies - PeopleFlow HRMS')]
class MyPolicies extends Component
{
    public array $policies = [];
    public $employee;

    public function mount()
    {
        $user = Auth::user();
        $this->employee = Employee::where('email', $user->email)->first();

        $definitions = PolicyDefinitions::all();
        
        foreach ($definitions as $slug => $definition) {
            if ($slug === 'holiday') continue;

            $modelClass = $definition['model'];
            $policy = $modelClass::where('is_active', true)->first();

            if ($policy) {
                $this->policies[] = [
                    'slug' => $slug,
                    'title' => $definition['title'],
                    'description' => $policy->description ?? $definition['description'],
                    'record' => $policy,
                    'fields' => $definition['fields'],
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.policies.my-policies');
    }
}
