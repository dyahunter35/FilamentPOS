<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;
    protected static string $view = 'filament.resources.order-resource.print-order';

    public function getTitle(): string
    {
        return $this->record->number; // Example: "Order #123 Details"
    }

    protected function getHeaderActions(): array
    {
        return [

            Actions\EditAction::make()->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            Actions\Action::make('print')
                ->label(trans('filament-invoices::messages.invoices.actions.print'))
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function () {
                    $this->js('window.print()');
                }),

              ];
    }
}
