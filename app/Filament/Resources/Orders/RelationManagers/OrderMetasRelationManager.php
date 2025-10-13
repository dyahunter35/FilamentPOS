<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\OrderStatus;
use App\Enums\Payment;
use App\Filament\Forms\Components\DecimalInput;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderMetasRelationManager extends RelationManager
{
    protected static string $relationship = 'orderMetas';
    protected static ?string $title = 'Payments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-invoices::messages.invoices.payments.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-invoices::messages.invoices.payments.title');
    }

    public static function getModelLabel(): ?string
    {
        return trans('filament-invoices::messages.invoices.payments.single');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('key', 'payments');
            })
            ->columns([
                TextColumn::make('created_at')
                    ->label(trans('filament-invoices::messages.invoices.payments.columns.created_at'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('value')
                    ->label(trans('filament-invoices::messages.invoices.payments.columns.amount'))
                    ->money(locale: 'en')
                    ->badge()
                    ->color('success')
                    ->numeric()
                    ->formatStateUsing(fn($state) => (string)number_format($state, 2))
                    ->sortable(),

                TextColumn::make('group')
                    ->formatStateUsing(fn($state) => Payment::tryFrom($state)->getLabel() ?? $state)
                    ->badge()

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('pay')->icon('heroicon-o-credit-card')
                    ->label(__('order.actions.pay.label'))
                    ->modalHeading(__('order.actions.pay.modal.heading'))
                    ->tooltip(__('order.actions.pay.label'))
                    ->color('info')
                    ->visible(fn() => ($this->ownerRecord->total != $this->ownerRecord->paid) || $this->ownerRecord->status === OrderStatus::Processing || $this->ownerRecord->status === OrderStatus::New)
                    ->requiresConfirmation()
                    ->fillForm(fn() => [
                        'total' => $this->ownerRecord->total,
                        'paid' => $this->ownerRecord->paid,
                        'amount' => $this->ownerRecord->total - $this->ownerRecord->paid,
                    ])
                    ->schema([
                        DecimalInput::make('total')
                            ->label(__('order.fields.total.label'))
                            ->disabled(),
                        DecimalInput::make('paid')
                            ->label(__('order.fields.paid.label'))

                            ->disabled(),
                        Select::make('payment_method')
                            ->label(__('order.fields.payment_method.label'))
                            ->options(Payment::toArray())
                            ->default('cash')
                            ->required()
                            ,
                        TextInput::make('amount')
                            ->label(__('order.fields.amount.label'))
                            ->required()
                            ->live(onBlur: true)
                            ->hint(fn($state) => number_format($state))
                            ->hintColor('info')
                            ->numeric()
                            ->maxValue($this->ownerRecord->total - $this->ownerRecord->paid)
                            ->rules(['regex:/^-?\d+(\.\d{1,2})?$/'])
                    ])
                    ->action(function (array $data, Order $ownerRecord) {

                        $this->ownerRecord->update([
                            'paid' => $this->ownerRecord->paid + $data['amount']
                        ]);

                        $this->ownerRecord->orderMetas()->create([
                            'key' => 'payments',
                            'group' => $data['payment_method'],
                            'value' => $data['amount']
                        ]);

                        $this->ownerRecord->orderLogs()->create([
                            'log' => 'Paid ' . number_format($data['amount'], 2) . ' ' . $this->ownerRecord->currency . ' By: ' . auth()->user()->name,
                            'type' => 'payment',
                        ]);

                        if ($this->ownerRecord->total === $this->ownerRecord->paid) {
                            $this->ownerRecord->update([
                                'status' => OrderStatus::Payed
                            ]);
                        }

                        Notification::make()
                            ->title(trans('filament-invoices::messages.invoices.actions.pay.notification.title'))
                            ->body(trans('filament-invoices::messages.invoices.actions.pay.notification.body'))
                            ->success()
                            ->send();
                        return redirect(request()->header('Referer'));
                    }),
            ]) // <-- التصحيح الأول: تم إغلاق ->headerActions هنا
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
