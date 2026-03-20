<?php

namespace App\Http\Controllers\Policies;

use App\Http\Controllers\Controller;

use App\Support\PolicyDefinitions;
use Illuminate\Http\JsonResponse;
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
        $allDefinitions = PolicyDefinitions::all();
        $types = collect($allDefinitions)
            ->reject(fn($config, $type) => $type === 'holiday') // Handled specifically
            ->map(function (array $config, string $type) {
                $modelClass = $config['model'];
                $defaultCode = $this->defaultCode($type);
                $policy = $modelClass::query()->where('code', $defaultCode)->first();
                
                if (!$policy) {
                    $policy = $modelClass::query()->create([
                        'name'        => $config['title'] . ' (Default)',
                        'code'        => $defaultCode,
                        'description' => $config['description'],
                        'is_active'   => true,
                        'created_by'  => auth()->id(),
                        'updated_by'  => auth()->id(),
                    ]);
                }

                return [
                    'type'        => $type,
                    'title'       => $config['title'],
                    'description' => $config['description'],
                    'policy'      => $policy,
                    'definition'  => $config,
                    'route'       => route('policies.update', $type),
                ];
            })
            ->values();

        // Fetch Holiday Policies for the special card
        $holidayPolicies = \App\Models\HolidayPolicy::withCount('holidayDates')->get();

        // Fetch Departments and Roles for dropdowns
        $departments = \App\Models\Department::all();
        $roles = \App\Models\Role::all();

        return view('hrms.policies.index', compact('types', 'holidayPolicies', 'departments', 'roles'));
    }

    public function edit(string $type): RedirectResponse
    {
        return redirect()->route('policies.index');
    }

    public function update(Request $request, string $type): RedirectResponse|JsonResponse
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $definition['title'] . ' updated successfully.',
            ]);
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

            if (($field['type'] ?? '') === 'json') {
                $value = $validated[$name] ?? null;
                // Treat empty strings as pure null so the DB handles it instead of throwing syntax errors on `{}` mapping.
                if ($value === '' || $value === null) {
                    $validated[$name] = null;
                } elseif (is_string($value)) {
                    $decoded = json_decode($value, true);
                    // Standardize empty json inputs to null for the database column
                    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                        // The Request validation 'json' rule already passed, so this shouldn't technically happen,
                        // but better safe than sorry.
                        $validated[$name] = null;
                    } else {
                        $validated[$name] = $decoded;
                    }
                }
            }
        }

        return $validated;
    }
}
