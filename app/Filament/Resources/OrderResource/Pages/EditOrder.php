<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Product;
use App\Services\InventoryService;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Support\Exceptions\Halt;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    
    /**
     * This method runs for both Create and Update.
     * It cleans up the data before it's saved.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['caused_by'] = auth()->id();
        // If the order is for a registered customer...
        if ($data['is_guest'] === false) {
            // ...ensure the guest_customer field is null.
            $data['guest_customer'] = null;
        } else {
            // Otherwise, if it's a guest, ensure customer_id is null.
            $data['customer_id'] = null;
        }


        // We don't need the is_guest flag in the database
        //unset($data['is_guest']);

        return $data;
    }

    /**
     * This is the main logic for handling the update process.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $inventoryService = new InventoryService();
        $currentBranch = Filament::getTenant();
        $currentUser = auth()->user();

        // Get the new state of items from the form
        $newItemsData = $this->form->getState()['items'] ?? [];

        // Get the original items before the update
        $originalItems = $record->items()->get()->keyBy('product_id');

        return DB::transaction(function () use ($record, $data, $newItemsData, $originalItems, $inventoryService, $currentBranch, $currentUser) {

            // --- 1. Restock original items ---
            // We return all original items to stock to simplify the logic.
            // Then we will deduct the new quantities.
            foreach ($originalItems as $item) {
                $product = Product::find($item->product_id);
                $inventoryService->addStockForBranch(
                    $product,
                    $currentBranch,
                    $item->qty,
                    "Order Update #{$record->number}",
                    $currentUser
                );
            }

            // --- 2. Check stock availability for the new set of items ---
            if (empty($newItemsData)) {
                Notification::make()->title(__('order.actions.create.notifications.at_least_one'))->warning()->send();
                throw new Halt();
            }

            foreach ($newItemsData as $newItem) {
                $product = Product::find($newItem['product_id']);
                if (!$inventoryService->isAvailableInBranch($product, $currentBranch, $newItem['qty'])) {
                    Notification::make()
                        ->title(__('order.actions.create.notifications.stock.title'))
                        ->body(__('order.actions.create.notifications.stock.message', ['product' => $product->name]))
                        ->danger()
                        ->send();

                    // IMPORTANT: Rollback stock additions before halting
                    // This is a simplified rollback. For production, a more robust mechanism is needed.
                    // For now, we halt, and the transaction will handle the full rollback.
                    throw new Halt('Stock not available, transaction rolled back.');
                }
            }

            // --- 3. Update the main order record ---
            $record->update($data);

            // --- 4. Delete old items and deduct stock for new items ---
            $record->items()->delete(); // Clear out all old items

            foreach ($newItemsData as $itemData) {
                // Create the new order item
                $record->items()->create($itemData);

                // Deduct the stock for the new item quantity
                $productToDeduct = Product::find($itemData['product_id']);
                $inventoryService->deductStockForBranch(
                    $productToDeduct,
                    $currentBranch,
                    $itemData['qty'],
                    "Order Update #{$record->number}",
                    $currentUser
                );
            }

            $inventoryService->updateAllBranches();

            // --- 5. Add an update log ---
            $record->orderLogs()->create([
                'log' => "Invoice updated By: " . $currentUser->name,
                'type' => 'updated'
            ]);

            return $record;
        });
    }
}
