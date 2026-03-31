<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Solutions - PeopleFlow HRMS')]
class Solutions extends Component
{
    public function render()
    {
        return view('livewire.public.solutions');
    }
}
