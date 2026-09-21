<?php

namespace App\Livewire;

use App\Models\Child;
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
        return view('livewire.foundation', [
            'child' => Child::query()
                ->with('latestAttendanceRecord')
                ->where('first_name', 'Maya')
                ->where('last_name', 'Dela Cruz')
                ->first(),
        ]);
    }
}
