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

            Actions\Action::make('pay')
                ->visible(fn($record) => ($record->total != $record->paid) || $record->status === OrderStatus::Processing || $record->status === OrderStatus::New)
                ->requiresConfirmation()
                ->color('info')
                ->fillForm(fn($record) => [
                    'total' => $record->total,
                    'paid' => $record->paid,
                    'amount' => $record->total - $record->paid,
                ])
                ->form([
                    Forms\Components\TextInput::make('total')
                        ->label(trans('filament-invoices::messages.invoices.actions.total'))
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('paid')
                        ->label(trans('filament-invoices::messages.invoices.actions.paid'))
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('amount')
                        ->label(trans('filament-invoices::messages.invoices.actions.amount'))
                        ->required()
                        ->numeric(),
                ])
                ->action(function (array $data, Order $record) {
                    $record->update([
                        'paid' => $record->paid + $data['amount']
                    ]);

                    $record->orderMetas()->create([
                        'key' => 'payments',
                        'value' => $data['amount']
                    ]);

                    $record->orderLogs()->create([
                        'log' => 'Paid ' . number_format($data['amount'], 2) . ' ' . $record->currency . ' By: ' . auth()->user()->name,
                        'type' => 'payment',
                    ]);

                    if ($record->total === $record->paid) {
                        $record->update([
                            'status' => OrderStatus::Payed
                        ]);
                    }

                    Notification::make()
                        ->title(trans('filament-invoices::messages.invoices.actions.pay.notification.title'))
                        ->body(trans('filament-invoices::messages.invoices.actions.pay.notification.body'))
                        ->success()
                        ->send();
                })
                ->icon('heroicon-o-credit-card')
                ->label(trans('pay'))
                ->modalHeading(trans('filament-invoices::messages.invoices.actions.pay.label'))
                ->tooltip(trans('filament-invoices::messages.invoices.actions.pay.label')),


        ];
    }
}
