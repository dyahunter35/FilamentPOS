<?php

namespace App\Filament\Resources\StockHistories\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\StockHistories\StockHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockHistories extends ListRecords
{
    protected static string $resource = StockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
