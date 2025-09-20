<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
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
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * This method runs AFTER the form is submitted but BEFORE the update happens.
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

        return $data;
    }

    /**
     * This is the main logic for handling the update process.
     * Overriding this gives us full control over the stock management transaction.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $inventoryService = new InventoryService();
        $currentBranch = Filament::getTenant();
        $currentUser = auth()->user();

        // Get the new state of items from the form data
        // With ->relationship(), Filament prepares the data for the relation.
        $newItemsData = $data['items'] ?? [];

        return DB::transaction(function () use ($record, $data, $newItemsData, $inventoryService, $currentBranch, $currentUser) {

            // Get the original items before any changes
            $originalItems = $record->items()->get();

            // --- 1. Restock original items ---
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

            if (($record->total != $data['total'])) {
                $data['status'] = OrderStatus::Processing;
            }

            // --- 2. Update the main order and its related items ---
            // Filament's default update process will handle creating, updating,
            // and deleting the related 'items' records automatically.
            $record->update($data);

            // --- 3. Deduct stock for the new set of items ---
            // We need to refresh the record to get the newly saved items
            $record->refresh();
            $newItemsFromDB = $record->items;

            if ($newItemsFromDB->isEmpty()) {
                Notification::make()->title(__('order.actions.create.notifications.at_least_one'))->warning()->send();
                //throw new Halt();
            }

            foreach ($newItemsFromDB as $newItem) {
                $product = Product::find($newItem->product_id);
                /* if (!$inventoryService->isAvailableInBranch($product, $currentBranch, $newItem->qty)) {
                    Notification::make()
                        ->title(__('order.actions.create.notifications.stock.title'))
                        ->body(__('order.actions.create.notifications.stock.message', ['product' => $product->name]))
                        ->danger()
                        ->send();
                    throw new Halt('Stock not available, transaction rolled back.');
                } */

                $inventoryService->deductStockForBranch(
                    $product,
                    $currentBranch,
                    $newItem->qty,
                    "Order Update #{$record->number}",
                    $currentUser
                );
            }

            $inventoryService->updateAllBranches();

            // --- 4. Add an update log ---
            $record->orderLogs()->create([
                'log' => "Invoice updated By: " . $currentUser->name,
                'type' => 'updated'
            ]);

            return $record;
        });
    }
}
