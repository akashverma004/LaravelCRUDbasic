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

    public function edit(): RedirectResponse
    {
        return redirect()->route('policies.index');
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        // Legacy update route - redirecting to hub
        return redirect()->route('policies.index');
    }
}
