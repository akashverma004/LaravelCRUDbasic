<?php

namespace App\Http\Controllers\Policies;

use App\Http\Controllers\Controller;

use App\Models\HolidayPolicy;
use App\Models\HolidayPolicyDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HolidayCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $policies = HolidayPolicy::query()
            ->withCount('holidayDates')
            ->orderBy('country_code')
            ->orderBy('state_code')
            ->orderBy('name')
            ->get();

        $selectedPolicyId = (int) ($request->integer('policy_id') ?: ($policies->first()->id ?? 0));
        $selectedPolicy = $selectedPolicyId > 0
            ? HolidayPolicy::query()
                ->with(['holidayDates' => fn ($query) => $query->orderBy('holiday_date')])
                ->find($selectedPolicyId)
            : null;

        return view('hrms.policies.holiday-calendar', [
            'policies' => $policies,
            'selectedPolicy' => $selectedPolicy,
        ]);
    }

    public function storeDate(Request $request, int $holidayPolicy): RedirectResponse|JsonResponse
    {
        $holidayPolicy = HolidayPolicy::query()->findOrFail($holidayPolicy);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'holiday_date' => ['required', 'date'],
            'is_optional' => ['sometimes', 'boolean'],
        ]);

        $date = HolidayPolicyDate::query()->create([
            'holiday_policy_id' => $holidayPolicy->id,
            'name' => $validated['name'],
            'holiday_date' => $validated['holiday_date'],
            'is_optional' => (bool) ($validated['is_optional'] ?? false),
            'rules' => [],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Holiday added.',
                'holiday_date' => $this->transformHolidayDate($date),
            ]);
        }

        return redirect()
            ->route('policies.holiday-calendar.index', ['policy_id' => $holidayPolicy->id])
            ->with('status', 'Holiday added.');
    }

    public function updateDate(Request $request, int $holidayPolicy, int $holidayDate): RedirectResponse|JsonResponse
    {
        $holidayPolicy = HolidayPolicy::query()->findOrFail($holidayPolicy);
        $holidayDate = HolidayPolicyDate::query()->findOrFail($holidayDate);
        abort_unless($holidayDate->holiday_policy_id === $holidayPolicy->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'holiday_date' => ['required', 'date'],
            'is_optional' => ['sometimes', 'boolean'],
        ]);

        $holidayDate->update([
            'name' => $validated['name'],
            'holiday_date' => $validated['holiday_date'],
            'is_optional' => (bool) ($validated['is_optional'] ?? false),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Holiday updated.',
                'holiday_date' => $this->transformHolidayDate($holidayDate->fresh()),
            ]);
        }

        return redirect()
            ->route('policies.holiday-calendar.index', ['policy_id' => $holidayPolicy->id])
            ->with('status', 'Holiday updated.');
    }

    public function destroyDate(Request $request, int $holidayPolicy, int $holidayDate): RedirectResponse|JsonResponse
    {
        $holidayPolicy = HolidayPolicy::query()->findOrFail($holidayPolicy);
        $holidayDate = HolidayPolicyDate::query()->findOrFail($holidayDate);
        abort_unless($holidayDate->holiday_policy_id === $holidayPolicy->id, 404);
        $holidayDate->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Holiday removed.',
            ]);
        }

        return redirect()
            ->route('policies.holiday-calendar.index', ['policy_id' => $holidayPolicy->id])
            ->with('status', 'Holiday removed.');
    }

    private function transformHolidayDate(HolidayPolicyDate $holidayDate): array
    {
        return [
            'id' => $holidayDate->id,
            'name' => $holidayDate->name,
            'holiday_date' => $holidayDate->holiday_date?->toDateString(),
            'is_optional' => (bool) $holidayDate->is_optional,
        ];
    }
}
