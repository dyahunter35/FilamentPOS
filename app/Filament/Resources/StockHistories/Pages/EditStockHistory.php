<?php

namespace App\Filament\Resources\StockHistories\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\StockHistories\StockHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockHistory extends EditRecord
{
    protected static string $resource = StockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
