<?php

namespace App\Http\Controllers;

use App\Models\HolidayPolicy;
use App\Support\GeoLookup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HolidayPolicyController extends Controller
{
    public function index(): View
    {
        $policies = HolidayPolicy::query()
            ->withCount('holidayDates')
            ->orderBy('country_code')
            ->orderBy('state_code')
            ->orderBy('name')
            ->get();

        return view('hrms.policies.holiday-policies', [
            'policies' => $policies,
            'weekdays' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            'countries' => config('geo.countries', []),
            'states' => config('geo.states_in', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePolicyPayload($request);

        $country = Str::upper($validated['country_code']);
        $state = Str::upper($validated['state_code']);

        HolidayPolicy::query()->create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?: sprintf('HOLIDAY_%s_%s_%s', $country, $state, Str::upper(Str::random(4))),
            'description' => $validated['description'] ?? null,
            'country_code' => $country,
            'state_code' => $state,
            'weekend_days' => $validated['weekend_days'] ?? ['saturday', 'sunday'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()
            ->route('policies.holiday-policies.index')
            ->with('status', 'Holiday policy created.');
    }

    public function update(Request $request, int $holidayPolicy): RedirectResponse
    {
        $holidayPolicy = HolidayPolicy::query()->findOrFail($holidayPolicy);
        $validated = $this->validatePolicyPayload($request);

        $holidayPolicy->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ? Str::upper($validated['code']) : $holidayPolicy->code,
            'description' => $validated['description'] ?? null,
            'country_code' => Str::upper($validated['country_code']),
            'state_code' => Str::upper($validated['state_code']),
            'weekend_days' => $validated['weekend_days'] ?? [],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route('policies.holiday-policies.index')
            ->with('status', 'Holiday policy updated.');
    }

    public function destroy(int $holidayPolicy): RedirectResponse
    {
        $holidayPolicy = HolidayPolicy::query()->findOrFail($holidayPolicy);
        $holidayPolicy->forceDelete();

        return redirect()
            ->route('policies.holiday-policies.index')
            ->with('status', 'Holiday policy deleted.');
    }

    private function validatePolicyPayload(Request $request): array
    {
        $request->merge([
            'country_code' => GeoLookup::normalizeCountryCode($request->input('country_code')),
            'state_code' => GeoLookup::normalizeIndianStateCode($request->input('state_code')),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
            'country_code' => ['required', Rule::in(array_keys(config('geo.countries', [])))],
            'state_code' => ['required', Rule::in(array_keys(config('geo.states_in', [])))],
            'weekend_days' => ['nullable', 'array'],
            'weekend_days.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
