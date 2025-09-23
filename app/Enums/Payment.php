<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Payment: string implements HasColor, HasIcon, HasLabel
{
    case Cash = 'cash';

    case Bok = 'bok';

    case Refund = 'refund';

    public function getLabel(): string
    {
        return __('order.fields.payment_method.options.'.$this->value);
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Cash => 'info',
            self::Bok => 'warning',
            self::Refund => 'warning',

        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Cash => 'heroicon-m-currency-dollar',
            self::Bok => 'heroicon-m-credit-card',
            self::Refund => 'heroicon-m-credit-card',
        };
    }

    public static function toArray(): array
    {
        return array_combine(
            array_map(fn (self $payment) => $payment->value, self::cases()),
            array_map(fn (self $payment) => $payment->getLabel(), self::cases()),
        );
    }
}
