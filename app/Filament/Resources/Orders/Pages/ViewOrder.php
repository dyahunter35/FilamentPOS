<?php

namespace App\Filament\Resources\Orders\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Forms;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;
    protected string $view = 'filament.resources.order-resource.print-order';

    public function getTitle(): string
    {
        return $this->record->number; // Example: "Order #123 Details"
    }

    protected function getHeaderActions(): array
    {
        return [

            EditAction::make()->icon('heroicon-o-pencil'),
            DeleteAction::make()->icon('heroicon-o-trash'),
            Action::make('print')
                ->label(trans('filament-invoices::messages.invoices.actions.print'))
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function () {
                    $this->js('window.print()');
                }),

              ];
    }
}
