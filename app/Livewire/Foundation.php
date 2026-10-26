<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Foundation extends Component
{
    public string $locale = 'en';

    public function toggleLanguage(): void
    {
        $this->locale = $this->locale === 'en' ? 'fil' : 'en';
    }

    public function render(): View
    {
        return view('livewire.foundation');
    }
}
