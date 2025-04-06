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

    case Installed = 'installed';

    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Processing => 'Processing',
            self::Payed => 'Processing',
            self::Delivered => 'Delivered',
            self::Installed => 'installed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::New => 'info',
            self::Processing => 'warning',
            self::Installed, self::Delivered,self::Payed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::New => 'heroicon-m-sparkles',
            self::Processing => 'heroicon-m-arrow-path',
            self::Delivered => 'heroicon-m-truck',
            self::Payed => 'heroicon-m-truck',
            self::Installed => 'heroicon-o-cog-6-tooth',
            self::Cancelled => 'heroicon-m-x-circle',
        };
    }
}
