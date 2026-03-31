<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Pricing - PeopleFlow HRMS')]
class Pricing extends Component
{
    public function render()
    {
        return view('livewire.public.pricing');
    }
}
