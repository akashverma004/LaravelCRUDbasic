<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForcePasswordChangeController extends Controller
{
    public function show()
    {
        return view('auth.force-password-change');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        
        $user->update([
            'password' => Hash::make($validated['password']),
            'require_password_change' => false,
        ]);

        return redirect()->route('dashboard')->with('status', 'Password updated successfully. Welcome aboard!');
    }
}
