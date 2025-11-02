<?php

namespace App\Filament\Resources\Orders\Pages;

use Exception;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Services\InventoryService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * This hook runs AFTER the order and its items have been successfully created.
     * This is the perfect place to handle stock deduction.
     */
    protected function afterCreate(): void
    {
        $inventoryService = new InventoryService();
        $currentBranch = Filament::getTenant();
        $currentUser = auth()->user();
        $order = $this->record;

        DB::transaction(function () use ($inventoryService, $currentBranch, $currentUser, $order) {
            if ($order->items->isEmpty()) {
                Notification::make()->title(__('order.actions.create.notifications.at_least_one'))->warning()->send();
                // We throw an exception to roll back the transaction
                throw new Exception('Cannot create an order with no items.');
            }

            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);

                // Final check for stock before deduction
                if (!$inventoryService->isAvailableInBranch($product, $currentBranch, $item->qty)) {
                    Notification::make()
                        ->title(__('order.actions.create.notifications.stock.title'))
                        ->body(__('order.actions.create.notifications.stock.message', ['product' => $product->name]))
                        ->danger()
                        ->send();
                    //throw new \Exception("Stock not available for {$product->name}.");
                    return;
                }

                // Deduct the stock
                $inventoryService->deductStockForBranch(
                    $product,
                    $currentBranch,
                    $item->qty,
                    "Order #{$order->number}",
                    $currentUser
                );
            }

            $inventoryService->updateAllBranches();

            $order->orderLogs()->create([
                'log' => "Invoice created By: " . $currentUser->name,
                'type' => 'created'
            ]);
        });
    }

    /**
     * This method runs BEFORE the main record is created.
     * It's used to prepare the data.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $currentBranch = Filament::getTenant();
        $currentUser = auth()->user();

        // Handle guest customer logic
        if (isset($data['is_guest'])) {
            if ($data['is_guest'] === false) {
                $data['guest_customer'] = null;
            } else {
                $data['customer_id'] = null;
            }
        }

        $data['number'] = Order::generateInvoiceNumber();
        $data['caused_by'] = $currentUser->id;
        $data['branch_id'] = $currentBranch->id;

        return $data;
    }
}
