<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Title;
use Livewire\Component;

class Register extends Component
{
    #[Title("Register")]
    public $name;
    public $email;
    public $password;
    public function save()
    {
        dd($this->password);
    }
    public function render()
    {
        return view('livewire.auth.register');
    }
}
