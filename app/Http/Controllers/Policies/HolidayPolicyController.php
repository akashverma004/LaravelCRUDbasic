<?php

namespace App\Http\Controllers\Policies;

use App\Http\Controllers\Controller;
use App\Models\HolidayPolicy;
use App\Support\GeoLookup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HolidayPolicyController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('policies.index');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $this->validatePolicyPayload($request);

        $country = Str::upper($validated['country_code']);
        $state = Str::upper($validated['state_code']);

        $policy = HolidayPolicy::query()->create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?: sprintf('HOLIDAY_%s_%s_%s', $country, $state, Str::upper(Str::random(4))),
            'description' => $validated['description'] ?? null,
            'country_code' => $country,
            'state_code' => $state,
            'weekend_days' => $validated['weekend_days'] ?? ['saturday', 'sunday'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Holiday policy created.',
                'policy' => $this->transformPolicy($policy->loadCount('holidayDates')),
            ]);
        }

        return redirect()
            ->route('policies.index')
            ->with('status', 'Holiday policy created.');
    }

    public function update(Request $request, int $holidayPolicy): RedirectResponse|JsonResponse
    {
        $policy = HolidayPolicy::query()->findOrFail($holidayPolicy);
        
        // Handle simple status toggles with checkboxes
        if ($request->has('is_active') && count($request->all()) <= 3) { // csrf, method, is_active
            $policy->update(['is_active' => (bool) $request->input('is_active')]);
             if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Policy status updated.']);
            }
            return redirect()->route('policies.index');
        }

        $validated = $this->validatePolicyPayload($request);

        $policy->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ? Str::upper($validated['code']) : $policy->code,
            'description' => $validated['description'] ?? null,
            'country_code' => Str::upper($validated['country_code']),
            'state_code' => Str::upper($validated['state_code']),
            'weekend_days' => $validated['weekend_days'] ?? [],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Holiday policy updated.',
                'policy' => $this->transformPolicy($policy->fresh()->loadCount('holidayDates')),
            ]);
        }

        return redirect()
            ->route('policies.index')
            ->with('status', 'Holiday policy updated.');
    }

    public function destroy(Request $request, int $holidayPolicy): RedirectResponse|JsonResponse
    {
        $policy = HolidayPolicy::query()->findOrFail($holidayPolicy);
        $policy->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Holiday policy deleted.',
            ]);
        }

        return redirect()
            ->route('policies.index')
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

    private function transformPolicy(HolidayPolicy $policy): array
    {
        return [
            'id' => $policy->id,
            'name' => $policy->name,
            'code' => $policy->code,
            'description' => $policy->description,
            'country_code' => $policy->country_code,
            'state_code' => $policy->state_code,
            'weekend_days' => $policy->weekend_days ?? [],
            'is_active' => (bool) $policy->is_active,
            'holiday_dates_count' => $policy->holiday_dates_count ?? 0,
            'calendar_url' => route('policies.holiday-calendar.index', ['policy_id' => $policy->id]),
        ];
    }
}
