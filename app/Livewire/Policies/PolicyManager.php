<?php

namespace App\Livewire\Policies;

use App\Support\PolicyDefinitions;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('hrms.layouts.app')]
#[Title('Policy Governance - PeopleFlow HRMS')]
class PolicyManager extends Component
{
    public $policies = [];
    public bool $showEditModal = false;
    public ?string $activeType = null;
    public array $activeDefinition = [];
    public array $formData = [];

    public function mount()
    {
        $this->loadPolicies();
    }

    public function loadPolicies()
    {
        $allDefinitions = PolicyDefinitions::all();
        $this->policies = collect($allDefinitions)
            ->reject(fn($config, $type) => $type === 'holiday')
            ->map(function ($config, $type) {
                $modelClass = $config['model'];
                $defaultCode = $this->defaultCode($type);
                $policy = $modelClass::where('code', $defaultCode)->first();
                
                if (!$policy) {
                    $policy = $modelClass::create([
                        'tenant_id' => Auth::user()->tenant_id,
                        'name' => $config['title'] . ' (Default)',
                        'code' => $defaultCode,
                        'description' => $config['description'],
                        'is_active' => true,
                        'created_by' => Auth::id(),
                    ]);
                }

                return [
                    'type' => $type,
                    'title' => $config['title'],
                    'description' => $config['description'],
                    'policy' => $policy,
                    'is_active' => $policy->is_active,
                ];
            })
            ->values()
            ->toArray();
    }

    private function defaultCode(string $type): string
    {
        return strtoupper(str_replace('-', '_', $type)) . '_DEFAULT';
    }

    public function openEditModal(string $type)
    {
        $this->activeType = $type;
        $this->activeDefinition = PolicyDefinitions::resolve($type);
        $modelClass = $this->activeDefinition['model'];
        $policy = $modelClass::where('code', $this->defaultCode($type))->first();

        // Initialize form data from model
        $this->formData = [];
        foreach ($this->activeDefinition['fields'] as $field) {
            $name = $field['name'];
            if ($name === 'code') continue;
            
            $val = $policy->$name;
            if ($field['type'] === 'json' && is_array($val)) {
                $val = json_encode($val, JSON_PRETTY_PRINT);
            }
            $this->formData[$name] = $val;
        }

        $this->showEditModal = true;
    }

    public function savePolicy()
    {
        $modelClass = $this->activeDefinition['model'];
        $policy = $modelClass::where('code', $this->defaultCode($this->activeType))->first();

        $payload = [];
        foreach ($this->activeDefinition['fields'] as $field) {
            $name = $field['name'];
            if ($name === 'code') continue;

            $val = $this->formData[$name] ?? null;

            if ($field['type'] === 'json') {
                $val = empty($val) ? null : json_decode($val, true);
            }

            if ($field['type'] === 'boolean') {
                $val = (bool)$val;
            }

            $payload[$name] = $val;
        }

        $payload['updated_by'] = Auth::id();
        $policy->update($payload);

        $this->showEditModal = false;
        $this->loadPolicies();
        $this->dispatch('notify', message: 'Corporate policy updated.', type: 'success');
    }

    public function render()
    {
        return view('livewire.policies.policy-manager');
    }
}
