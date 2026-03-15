<?php

namespace App\Http\Controllers\Policies;

use App\Http\Controllers\Controller;

use App\Services\Policies\LeavePolicyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeavePolicyController extends Controller
{
    public function __construct(private LeavePolicyService $leavePolicyService)
    {
    }

    public function edit(): View
    {
        $policy = $this->leavePolicyService->all()->first();
        if (! $policy) {
            $policy = $this->leavePolicyService->create([
                'name' => 'Default Leave Policy',
                'code' => 'DEFAULT_LEAVE_POLICY',
                'annual_limit' => 12,
                'sick_limit' => 8,
                'casual_limit' => 6,
                'unpaid_limit' => 0,
                'is_active' => true,
            ], auth()->id());
        }

        return view('hrms.policies.leave', compact('policy'));
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'annual_limit' => ['required', 'integer', 'min:0', 'max:365'],
            'sick_limit' => ['required', 'integer', 'min:0', 'max:365'],
            'casual_limit' => ['required', 'integer', 'min:0', 'max:365'],
            'unpaid_limit' => ['required', 'integer', 'min:0', 'max:365'],
        ]);

        $policy = $this->leavePolicyService->all()->first();
        if (! $policy) {
            $this->leavePolicyService->create($validated + [
                'name' => 'Default Leave Policy',
                'code' => 'DEFAULT_LEAVE_POLICY',
                'is_active' => true,
            ], auth()->id());
        } else {
            $this->leavePolicyService->update($policy->id, $validated, auth()->id());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Global leave policy updated.',
            ]);
        }

        return redirect()->route('policies.leave.edit')->with('status', 'Global leave policy updated.');
    }
}
