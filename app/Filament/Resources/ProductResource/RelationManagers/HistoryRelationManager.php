<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class HistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'history';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Type of Change')
                    ->options([
                        'increase' => 'Increase (Add Stock)',
                        'decrease' => 'Decrease (Remove Stock)',
                        'initial' => 'Initial Stock Count',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('quantity_change')
                    ->label('Quantity')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes / Reason')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('branch_id')
                    ->default(Filament::getTenant()->id)

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'increase' => 'success',
                        'decrease' => 'danger',
                        'initial' => 'info',
                    }),

                Tables\Columns\TextColumn::make('quantity_change')
                    ->label('Quantity Changed'),

                Tables\Columns\TextColumn::make('new_quantity')
                    ->label('Stock After Change'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('notes'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // This uses the form defined above to create a new record
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // This hook runs just before the data is saved.
                        // We will manually calculate the new stock level.

                        /** @var \App\Models\Product $product */
                        $product = $this->getOwnerRecord();
                        $currentQty = $product->qty;
                        $change = $data['quantity_change'];

                        if ($data['type'] === 'increase') {
                            $newQty = $currentQty + $change;
                        } else {
                            $newQty = $currentQty - $change;
                        }

                        // Set the new quantity and user_id in the data array
                        $data['new_quantity'] = $newQty;
                        $data['user_id'] = Auth::id();

                        // IMPORTANT: Update the product's actual stock level
                        $product->update(['qty' => $newQty]);

                        return $data;
                    }),
            ])
            ->actions([
                // You can add actions like Edit or Delete if needed
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
