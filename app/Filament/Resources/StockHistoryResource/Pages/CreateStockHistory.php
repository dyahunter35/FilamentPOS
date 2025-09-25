<?php

namespace App\Filament\Resources\StockHistoryResource\Pages;

use App\Filament\Resources\StockHistoryResource;
use App\Models\Product;
use App\Services\InventoryService;
use Filament\Forms;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Enums\StockCase;
use Filament\Facades\Filament;

class CreateStockHistory extends CreateRecord
{
    protected static string $resource = StockHistoryResource::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('تحديث المخزون')
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label(__('stock_history.fields.product_id.label'))
                        ->options(
                            Product::whereHas('branches', fn($query) => $query->where('branches.id', Filament::getTenant()->id))
                                ->get()
                                ->mapWithKeys(fn(Product $product) => [
                                    $product->id => sprintf(
                                        '%s - %s [%s]',
                                        $product->name,
                                        $product->category?->name,
                                        $product->stock_for_current_branch
                                    )
                                ])
                        )
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
                        ->numeric()
                        ->required(),

                    Forms\Components\Textarea::make('notes')
                        ->label(__('stock_history.fields.notes.label'))
                        ->placeholder(__('stock_history.fields.notes.placeholder'))
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpan(2),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // أتركها فارغة إذا تريد استخدام Service فقط
        return $data;
    }

    protected function createRecord(array $data)
    {
        $data = $this->form->getState(); // هنا بنجيب بيانات الفورم
        $inventoryService = new InventoryService();
        $branch = Filament::getTenant();
        $user = Auth::user();
        $product = \App\Models\Product::find($data['product_id']);

        if (!$product) {
            Notification::make()
                ->title('المنتج غير موجود')
                ->danger()
                ->send();
            throw new \Exception('المنتج غير موجود');
        }

        try {
            if ($data['type'] === StockCase::Increase || $data['type'] === StockCase::Initial) {

                $inventoryService->addStockForBranch(
                    $product,
                    $branch,
                    $data['quantity_change'],
                    $data['notes'] ?? null,
                    $user
                );
            } else {
                $inventoryService->deductStockForBranch(
                    $product,
                    $branch,
                    $data['quantity_change'],
                    $data['notes'] ?? null,
                    $user
                );
            }

            Notification::make()
                ->title('تم تحديث المخزون بنجاح')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('خطأ في المخزون')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw $e; // أي خطأ سيوقف الـ transaction
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
