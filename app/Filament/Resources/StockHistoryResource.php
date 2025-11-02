<?php

namespace App\Filament\Resources;

use App\Enums\StockCase;
use App\Filament\Resources\StockHistoryResource\Pages;
use App\Filament\Resources\StockHistoryResource\RelationManagers;
use App\Models\Product;
use App\Models\StockHistory;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockHistoryResource extends Resource
{
    protected static ?string $model = StockHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $isScopedToTenant = true;

    protected static ?int $navigationSort = 5;

    // --- NAVIGATION ---
    public static function getModelLabel(): string
    {
        return __('stock_history.label.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('stock_history.label.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('stock_history.label.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('product.navigation.group');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([

                        Forms\Components\Select::make('product_id')
                            ->label(__('stock_history.fields.product_id.label'))
                            ->options(Product::whereHas('branches', fn($query) => $query->where('branches.id', Filament::getTenant()->id))
                                ->get()
                                ->mapWithKeys(fn(Product $product) => [
                                    $product->id => sprintf(
                                        '%s - %s [%s]',
                                        $product->name,
                                        $product->category?->name,
                                        $product->stock_for_current_branch
                                    )
                                ]))
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label(__('stock_history.fields.type.label'))
                            ->required()
                            ->options(StockCase::class)
                            ->default(StockCase::Increase),
                        Forms\Components\TextInput::make('quantity_change')
                            ->label(__('stock_history.fields.quantity_change.label'))
                            ->placeholder(__('stock_history.fields.quantity_change.placeholder'))
                            ->required()
                            ->numeric(),

                        Forms\Components\Textarea::make('notes')
                            ->label(__('stock_history.fields.notes.label'))
                            ->placeholder(__('stock_history.fields.quantity_change.placeholder'))
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpan(2)

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(self::getEloquentQuery()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('stock_history.fields.product_id.label'))

                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('stock_history.fields.type.label'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity_change')
                    ->label(__('stock_history.fields.quantity_change.label'))
                    ->formatStateUsing(fn(string $state): string => number_format($state))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label(__('stock_history.fields.notes.label'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('stock_history.fields.user.label'))
                    ->visible(auth()->user()->hasRole('مدير'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('stock_history.fields.created_at.label'))

                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('stock_history.fields.updated_at.label'))

                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockHistories::route('/'),
            'create' => Pages\CreateStockHistory::route('/create'),
            'edit' => Pages\EditStockHistory::route('/{record}/edit'),
        ];
    }
}
