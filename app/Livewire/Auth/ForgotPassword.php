<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Title;
use Livewire\Component;

class ForgotPassword extends Component
{
    #[Title('Forgot password')]
    public $email;

    public function save()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email|max:255'
        ]);
        $status = Password::sendResetLink(['email' => $this->email]);
        if ($status === Password::RESET_LINK_SENT) {
            $this->addError('success', 'Password reset link has been sent to your email address!');
            $this->email = '';
            // return redirect()->back()->with('success', session('success'));
        }
    }
    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
