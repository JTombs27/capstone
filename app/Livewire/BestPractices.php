<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Best Practices - DDO-ADS-TRACE')]
class BestPractices extends Component
{
    public function render()
    {
        return view('livewire.best-practices');
    }
}
