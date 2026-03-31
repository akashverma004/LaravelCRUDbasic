<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Documentation - PeopleFlow HRMS')]
class Docs extends Component
{
    public function render()
    {
        return view('livewire.public.docs');
    }
}
