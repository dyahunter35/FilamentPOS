<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasColor, HasIcon, HasLabel
{
    case Male = 'male';

    case Female = 'female';

    public function getLabel(): string
    {
        return __('customer.fields.gender.options.'.$this->value);
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Male => 'info',
            self::Female => 'warning',
            
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Male => 'heroicon-m-sparkles',
            self::Female => 'heroicon-m-arrow-path',
        };
    }
}
