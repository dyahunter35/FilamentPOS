<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class DecimalInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
        $this->inputMode('decimal');
        $this->step('0.01');
        $this->rules(['numeric', 'regex:/^\d+(\.\d{1,2})?$/']);
        $this->hint(fn($state) => number_format($state, 2));
        $this->hintColor('info');
        $this->default(0);
        //$this->mask(RawJs::make('$money($input)'));
        $this->stripCharacters(',');
    }
}
