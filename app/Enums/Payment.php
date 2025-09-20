<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Payment: string implements HasColor, HasIcon, HasLabel
{
    case Cash = 'cash';

    case Bok = 'bok';

    public function getLabel(): string
    {
        return __('order.fields.payment_method.options.'.$this->value);
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Cash => 'info',
            self::Bok => 'warning',

        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Cash => 'heroicon-m-currency-dollar',
            self::Bok => 'heroicon-m-credit-card',
        };
    }
}
