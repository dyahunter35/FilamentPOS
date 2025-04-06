<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
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
                    ->sortable(),

            ])
            ->filters([
                //
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
