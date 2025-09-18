<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StockCase: string implements HasColor, HasIcon, HasLabel
{

    case Initial = 'initial';
    case Increase = 'increase';
    case Decrease = 'decrease';

    public function getLabel(): string
    {
        return __('stock_history.fields.type.options.'.$this->value);
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Initial => 'info',
            self::Increase => 'success',
            self::Decrease => 'warning',

        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Initial => 'heroicon-m-sparkles',
            self::Increase => 'heroicon-m-arrow-trending-up',
            self::Decrease => 'heroicon-m-arrow-trending-down',
        };
    }
}
