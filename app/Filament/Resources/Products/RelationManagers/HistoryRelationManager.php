<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\StockCase;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
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
        $quantity = (auth()->user()->hasAnyRole(['مدير', 'super_admin'])) ? ' (' . $ownerRecord->total_stock  . ')' : '';
        return __('stock_history.label.plural') . $quantity;
    }

    protected static function getPluralRecordLabel(): ?string
    {
        return __('stock_history.label.plural');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('stock_history.label.single');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(__('stock_history.fields.type.label'))
                    ->options(StockCase::class)
                    ->required(),

                TextInput::make('quantity_change')
                    ->label(__('stock_history.fields.quantity_change.label'))
                    ->placeholder(__('stock_history.fields.quantity_change.placeholder'))
                    ->numeric()
                    ->required()
                    ->minValue(1),

                Textarea::make('notes')
                    ->label(__('stock_history.fields.notes.label'))
                    ->columnSpanFull(),

                Hidden::make('branch_id')
                    ->default(Filament::getTenant()->id)

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('stock_history.fields.created_at.label'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('stock_history.fields.type.label'))
                    ->badge(),

                TextColumn::make('quantity_change')
                    ->label(__('stock_history.fields.quantity_change.label')),

                /*  Tables\Columns\TextColumn::make('new_quantity')
                    ->label(__('stock_history.fields.quantity_after_change.label')), */

                TextColumn::make('user.name')
                    ->label(__('stock_history.fields.user.label'))
                    ->placeholder('N/A'),

                TextColumn::make('notes')
                    ->label(__('stock_history.fields.notes.label')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // This uses the form defined above to create a new record
                CreateAction::make()
                    ->visible(fn() => $this->ownerRecord->branches()->where('branch_id', Filament::getTenant()->id)->exists())
                    ->using(function (array $data, RelationManager $livewire): Model {
                        $inventoryService = new InventoryService();
                        $product = $livewire->getOwnerRecord();
                        $branch = Filament::getTenant(); // افتراض أنك تعمل داخل tenant
                        $user = Auth::user();

                        if ($data['type'] === 'increase' || $data['type'] === 'initial') {
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
                    }),
            ])
            ->recordActions([
                // You can add actions like Edit or Delete if needed
                //Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
