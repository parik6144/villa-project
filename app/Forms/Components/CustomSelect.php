<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Livewire;

class CustomSelect extends Select
{
    protected string $view = 'forms.components.custom-select';

    protected array $airports = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->reactive();

    }
}
