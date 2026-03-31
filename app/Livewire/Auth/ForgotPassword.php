<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Forgot Password - PeopleFlow HRMS')]
class ForgotPassword extends Component
{
    public string $email = '';
    public ?string $status = null;

    public function sendResetLink()
    {
        $this->validate(['email' => ['required', 'email']]);

        $status = Password::broker()->sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->status = __($status);
            $this->reset('email');
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
