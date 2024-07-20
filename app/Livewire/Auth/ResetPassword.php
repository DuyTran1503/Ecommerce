<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

#[Title('Reset password')]
class ResetPassword extends Component
{
    #[Url]
    public $token;
    public $email;

    public $password;
    public $password_confirmation;
    public function mount(Request $request)
    {
        // Lấy email từ query string  
        $this->email = $request->query('email');
        $this->token = $request->query('token');
        if (!$this->token) {
            $segments = $request->segments();
            $this->token = end($segments);
        }
    }
    public function save()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        ]);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $password = $this->password;
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET ? redirect('/login') : session()->flash('error', 'An error occurred, please try again');
    }
    public function render()
    {
        return view('livewire.auth.reset');
    }
}
