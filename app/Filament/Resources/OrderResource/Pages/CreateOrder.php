<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Product;
use App\Services\InventoryService; // <-- استيراد الخدمة
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt; // <-- مهم لإيقاف العملية
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // <-- مهم للـ transactions

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * تم تعطيل هذه الدالة لأننا سنضع منطقها داخل handleRecordCreation
     * @param array $data
     * @return array
     */
    /* protected function mutateFormDataBeforeCreate(array $data): array
    {
        // ... Logic moved to handleRecordCreation
    } */

    /**
     * تم تعطيل هذه الدالة لأننا سنضع منطقها داخل handleRecordCreation
     */
    /* public function afterCreate()
    {
        // ... Logic moved to handleRecordCreation
    } */

    /**
     * التحكم الكامل في عملية إنشاء السجل لضمان سلامة البيانات والمخزون.
     *
     * @param array $data
     * @return Model
     * @throws Halt
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Get the full state of the form, including the repeater items
        $fullData = $this->form->getState();

        //dd($fullData);
        // Now you can access the items
        $orderItemsData = $fullData['items'] ?? [];

        $inventoryService = new InventoryService();
        $currentBranch = Filament::getTenant();
        $currentUser = auth()->user();

        // Start a transaction for the entire operation
        return DB::transaction(function () use ($inventoryService, $currentBranch, $currentUser, $data, $orderItemsData) {

            // --- 1. Stock Availability Check ---
            if (empty($orderItemsData)) {
                Notification::make()->title(__('order.actions.create.notifications.at_least_one'))->warning()->send();
                throw new Halt();
            }

            foreach ($orderItemsData as $item) {
                $product = Product::find($item['product_id']);
                if (!$inventoryService->isAvailableInBranch($product, $currentBranch, $item['qty'])) {
                    Notification::make()
                        ->title(__('order.actions.create.notifications.stock.title'))
                        ->body(__('order.actions.create.notifications.stock.message', ['product' => $product->name]))
                        ->danger()
                        ->send();

                    throw new Halt();
                }
            }

            // --- 2. Prepare Main Order Data ---
            // (The guest customer logic from your mutateFormDataBeforeCreate is here)
            if (isset($data['is_guest'])) {
                if ($data['is_guest'] === false) {
                    $data['guest_customer'] = null;
                } else {
                    $data['customer_id'] = null;
                }
                unset($data['is_guest']);
            }
            $data['caused_by'] = $currentUser->id;
            $data['branch_id'] = $currentBranch->id;

            // --- 3. Create the Main Order ---
            $order = static::getModel()::create($data);

            // --- 4. Create Order Items and Deduct Stock ---
            foreach ($orderItemsData as $itemData) {
                // Create the order item record and associate it with the order
                $order->items()->create($itemData);

                // Deduct the stock
                $productToDeduct = Product::find($itemData['product_id']);
                $inventoryService->deductStockForBranch(
                    $productToDeduct,
                    $currentBranch,
                    $itemData['qty'],
                    "Order #{$order->number}",
                    $currentUser
                );
            }

            $inventoryService->updateAllBranches();
            // You can add your order log creation here if you wish
            $order->orderLogs()->create([
                'log' => "Invoice created By: " . $currentUser->name,
                'type' => 'created'
            ]);

            return $order;
        });
    }
}
