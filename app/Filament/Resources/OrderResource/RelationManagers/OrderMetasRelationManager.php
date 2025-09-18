<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Enums\OrderStatus;
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

    /**
     * @return string|null
     */
    public static function getLabel(): ?string
    {
        return trans('filament-invoices::messages.invoices.payments.title');
    }

    /**
     * @return string|null
     */
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans('filament-invoices::messages.invoices.payments.columns.created_at'))
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label(trans('filament-invoices::messages.invoices.payments.columns.amount'))
                    ->money(locale: 'en')
                    ->badge()
                    ->color('success')
                    ->numeric()
                    ->formatStateUsing(fn($state) => (string)number_format($state, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('group')
                    ->badge()

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('pay')
                    ->visible(fn() => ($this->ownerRecord->total != $this->ownerRecord->paid) || $this->ownerRecord->status === OrderStatus::Processing || $this->ownerRecord->status === OrderStatus::New)
                    ->requiresConfirmation()
                    ->color('info')
                    ->fillForm(fn($record) => [
                        'total' => $this->ownerRecord->total,
                        'paid' => $this->ownerRecord->paid,
                        'amount' => $this->ownerRecord->total - $this->ownerRecord->paid,
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
                        Forms\Components\Select::make('payment_method')
                            ->label(__('order.fields.payment_method.label'))
                            ->options([
                                'cash' => __('order.fields.payment_method.options.cash'),
                                'bok' => __('order.fields.payment_method.options.bok'),
                            ])
                            ->required()
                            ->default('bok'),
                        Forms\Components\TextInput::make('amount')
                            ->label(trans('filament-invoices::messages.invoices.actions.amount'))
                            ->required()
                            ->numeric(),
                    ])
                    ->action(function (array $data, Order $ownerRecord) {
                        if($data['amount']<=0)
                        {
                            Notification::make()->send();
                            return;
                        }
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
                    })
                    ->icon('heroicon-o-credit-card')
                    ->label(trans('pay'))
                    ->modalHeading(trans('filament-invoices::messages.invoices.actions.pay.label'))
                    ->tooltip(trans('filament-invoices::messages.invoices.actions.pay.label')),



            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
