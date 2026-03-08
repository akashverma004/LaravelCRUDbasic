<?php

namespace App\Http\Controllers;

use App\Support\PolicyDefinitions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PolicyManagementController extends Controller
{
    public function __construct()
    {
        // Only admins and HR managers may manage policies.
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->hasAnyRole(['admin', 'hr_manager']) && ! $user->is_platform_admin)) {
                abort(403, 'You do not have permission to manage policies.');
            }

            return $next($request);
        });
    }

    public function index(): View
    {
        $types = collect(PolicyDefinitions::all())
            ->map(fn (array $config, string $type) => [
                'type'        => $type,
                'title'       => $config['title'],
                'description' => $config['description'],
                'route'       => route('policies.edit', $type),
            ])
            ->values();

        return view('hrms.policies.index', compact('types'));
    }

    public function edit(string $type): View
    {
        $definition = PolicyDefinitions::resolve($type);
        $modelClass = $definition['model'];

        // Explicitly tenant-scoped: the BelongsToTenant global scope handles the WHERE.
        // firstOrCreate is keyed on (code) within the current tenant scope.
        $defaultCode = $this->defaultCode($type);
        $policy = $modelClass::query()->where('code', $defaultCode)->first();

        if (! $policy) {
            $policy = $modelClass::query()->create([
                'name'        => $definition['title'] . ' (Default)',
                'code'        => $defaultCode,
                'description' => $definition['description'],
                'is_active'   => true,
                'created_by'  => auth()->id(),
                'updated_by'  => auth()->id(),
            ]);
        }

        return view('hrms.policies.edit', [
            'type'       => $type,
            'definition' => $definition,
            'policy'     => $policy,
        ]);
    }

    public function update(Request $request, string $type): RedirectResponse
    {
        $definition  = PolicyDefinitions::resolve($type);
        $modelClass  = $definition['model'];
        $defaultCode = $this->defaultCode($type);

        // Strip 'code' from user-editable fields to prevent code tampering.
        $editableFields = array_filter(
            $definition['fields'],
            fn ($f) => $f['name'] !== 'code'
        );

        $rules     = $this->buildValidationRules($editableFields);
        $validated = $request->validate($rules);
        $payload   = $this->normalizePayload($validated, $editableFields);

        // Always stamp the updater.
        $payload['updated_by'] = auth()->id();

        // Tenant-scoped lookup — the global scope ensures we never touch another tenant's data.
        $policy = $modelClass::query()->where('code', $defaultCode)->first();

        if (! $policy) {
            $payload['name']       = $payload['name'] ?? ($definition['title'] . ' (Default)');
            $payload['code']       = $defaultCode;
            $payload['created_by'] = auth()->id();
            $modelClass::query()->create($payload);
        } else {
            $policy->update($payload);
        }

        return redirect()
            ->route('policies.edit', $type)
            ->with('status', $definition['title'] . ' updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Stable, tenant-independent code for the default policy of a given type.
     * Always uppercase, always the same regardless of who creates it.
     */
    private function defaultCode(string $type): string
    {
        return strtoupper(str_replace('-', '_', $type)) . '_DEFAULT';
    }

    private function buildValidationRules(array $fields): array
    {
        $rules = [];
        foreach ($fields as $field) {
            $name     = $field['name'];
            $type     = $field['type'];
            $required = $field['required'] ?? false;

            $base         = $required ? ['required'] : ['nullable'];
            $rules[$name] = match ($type) {
                'text'     => array_merge($base, ['string', 'max:255']),
                'textarea' => array_merge($base, ['string']),
                'date'     => array_merge($base, ['date']),
                'number'   => array_merge($base, ['numeric', 'min:0']),
                'integer'  => array_merge($base, ['integer', 'min:0']),
                'boolean'  => ['sometimes', 'boolean'],
                'select'   => array_merge($base, ['string']),
                'json'     => array_merge($base, ['json']),
                default    => $base,
            };
        }

        return $rules;
    }

    private function normalizePayload(array $validated, array $fields): array
    {
        foreach ($fields as $field) {
            $name = $field['name'];
            if (! array_key_exists($name, $validated)) {
                if (($field['type'] ?? '') === 'boolean') {
                    $validated[$name] = false;
                }
                continue;
            }

            if (($field['type'] ?? '') === 'json' && is_string($validated[$name])) {
                $validated[$name] = json_decode($validated[$name], true);
            }
        }

        return $validated;
    }
}
