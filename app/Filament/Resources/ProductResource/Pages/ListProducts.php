<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('report')
                ->label(__('product.actions.report.label'))
                ->icon(__('heroicon-o-document'))
                ->color('info')
                ->url(ProductResource::getUrl('report')),

            Actions\Action::make('report')
                ->label(__('product.actions.branch_report.label'))
                ->icon(__('heroicon-m-printer'))
                ->color('success')
                ->url(ProductResource::getUrl('branch')),

            Actions\CreateAction::make(),
        ];
    }
}
