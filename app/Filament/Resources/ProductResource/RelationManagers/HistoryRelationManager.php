<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Enums\StockCase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\InventoryService; // <-- استيراد الكلاس
use Exception;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class HistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'history';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('stock_history.label.plural');
    }

    protected static function getPluralRecordLabel(): ?string
    {
        return __('stock_history.label.plural');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('stock_history.label.single');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label(__('stock_history.fields.type.label'))
                    ->options(StockCase::class)
                    ->required(),

                Forms\Components\TextInput::make('quantity_change')
                    ->label(__('stock_history.fields.quantity_change.label'))
                    ->placeholder(__('stock_history.fields.quantity_change.placeholder'))
                    ->numeric()
                    ->required()
                    ->minValue(1),

                Forms\Components\Textarea::make('notes')
                    ->label(__('stock_history.fields.notes.label'))
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
                    ->label(__('stock_history.fields.created_at.label'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('stock_history.fields.type.label'))
                    ->badge(),

                Tables\Columns\TextColumn::make('quantity_change')
                    ->label(__('stock_history.fields.quantity_change.label')),

                Tables\Columns\TextColumn::make('new_quantity')
                    ->label(__('stock_history.fields.quantity_after_change.label')),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('stock_history.fields.user.label'))
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('notes')
                    ->label(__('stock_history.fields.notes.label')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // This uses the form defined above to create a new record
                Tables\Actions\CreateAction::make()
                    ->visible(fn() => $this->ownerRecord->branches()->where('branch_id', Filament::getTenant()->id)->exists())
                    ->using(function (array $data, RelationManager $livewire): Model {
                        $inventoryService = new InventoryService();
                        $product = $livewire->getOwnerRecord();
                        $branch = Filament::getTenant(); // افتراض أنك تعمل داخل tenant
                        $user = Auth::user();

                        if ($data['type'] === 'increase') {
                            return $inventoryService->addStockForBranch(
                                $product,
                                $branch,
                                $data['quantity_change'],
                                $data['notes'],
                                $user
                            );
                        } else {
                            // يمكنك إضافة معالجة للـ exception هنا إذا أردت
                            try {
                                return $inventoryService->deductStockForBranch(
                                    $product,
                                    $branch,
                                    $data['quantity_change'],
                                    $data['notes'],
                                    $user
                                );
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('خطأ في المخزون')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();

                                throw new Halt();
                            }
                        }
                        $inventoryService->updateAllBranches();
                        if (($product->total_stock > $product->security_stock) && $product->low_stock_notified_at)
                            $product->update('low_stock_notified_at', null);
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
