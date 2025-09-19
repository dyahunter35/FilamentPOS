<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case New = 'new';

    case Processing = 'processing';

    case Payed = 'payed';

    case Delivered = 'delivered';

    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return __('order.fields.status.options.'.$this->value);
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::New => 'info',
            self::Processing => 'warning',
            self::Delivered,self::Payed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::New => 'heroicon-m-sparkles',
            self::Processing => 'heroicon-m-arrow-path',
            self::Delivered => 'heroicon-m-truck',
            self::Payed => 'heroicon-m-credit-card',
            self::Cancelled => 'heroicon-m-x-circle',
        };
    }
}
