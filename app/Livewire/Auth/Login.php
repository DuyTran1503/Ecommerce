<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;

class Login extends Component
{
    #[Title("Login")]
    public $email;
    public $password;
    public function login()
    {
        $this->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
        ]);
        if (!auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->addError('credentials', 'Account or password is incorrect, please check again');
            return;
        }
        return redirect()->intended();
    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
