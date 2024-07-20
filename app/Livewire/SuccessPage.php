<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Success page - Septenary Solution')]
class SuccessPage extends Component
{
    public function render()
    {
        return view('livewire.success-page');
    }
}
