<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $column = ($provider === 'azure') ? 'microsoft_id' : ($provider . '_id');
            
            $user = User::where($column, $socialUser->getId())
                ->orWhere('email', $socialUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    $column => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } else {
                // Check if an employee already exists with this email to get their tenant_id
                $employee = \App\Models\Employee::where('email', $socialUser->getEmail())->first();
                $tenantId = $employee ? $employee->tenant_id : 1;

                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'tenant_id' => $tenantId,
                    $column => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user);

            return redirect()->intended('dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Social Auth Error: ' . $e->getMessage(), [
                'provider' => $provider,
                'exception' => $e
            ]);
            return redirect()->route('login')->withErrors(['email' => 'Authentication failed. Please try again.']);
        }
    }
}
