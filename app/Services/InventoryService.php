<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Scopes\IsVisibleScope;
use App\Models\StockHistory;
use App\Models\User;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;

/**
 * Class InventoryService
 *
 * Handles all inventory logic based on a multi-tenant (Branch) system.
 * The source of truth for stock levels is the `branch_product` pivot table.
 * All changes are logged in the `stock_histories` table.
 */
class InventoryService
{
    /**
     * إضافة كمية إلى مخزون منتج في فرع معين.
     *
     * @param Product $product المنتج الذي ستتم إضافة المخزون إليه.
     * @param Branch $branch الفرع الذي سيتم تحديث المخزون فيه.
     * @param int $quantity الكمية المراد إضافتها.
     * @param string|null $notes ملاحظات حول العملية (مثال: "شحنة جديدة").
     * @param User|null $causer المستخدم الذي قام بالعملية.
     * @return StockHistory السجل التاريخي الذي تم إنشاؤه.
     */
    public function addStockForBranch(Product $product, Branch $branch, int $quantity, ?string $notes = 'Manual Update', ?User $causer = null): StockHistory
    {
        if ($quantity <= 0) {
            throw new Exception('الكمية يجب أن تكون أكبر من صفر.');
        }

        // استخدام transaction لضمان تنفيذ العملية بالكامل.
        return DB::transaction(function () use ($product, $branch, $quantity, $notes, $causer) {

            // جلب الكمية الحالية من الجدول الوسيط.
            $pivot = $product->branches()->find($branch->id)?->pivot;
            $currentQty = $pivot->total_quantity ?? 0;
            $newQty = $currentQty + $quantity;

            // إنشاء سجل في جدول تاريخ المخزون.
            // هذا السجل سيقوم تلقائياً بتحديث `total_quantity` في الجدول الوسيط
            // عبر الـ event listener الموجود في موديل StockHistory.
            return StockHistory::create([
                'product_id' => $product->id,
                'branch_id' => $branch->id,
                'type' => 'increase',
                'quantity_change' => $quantity,
                'new_quantity' => $newQty,
                'notes' => $notes,
                'user_id' => $causer?->id,
            ]);
        });
    }

    /**
     * خصم كمية من مخزون منتج في فرع معين بطريقة آمنة.
     *
     * @param Product $product المنتج الذي سيتم خصم المخزون منه.
     * @param Branch $branch الفرع الذي سيتم تحديث المخزون فيه.
     * @param int $quantity الكمية المراد خصمها.
     * @param string|null $notes ملاحظات حول العملية (مثال: "Order #123").
     * @param User|null $causer المستخدم الذي قام بالعملية.
     * @return StockHistory السجل التاريخي الذي تم إنشاؤه.
     * @throws Exception إذا كانت الكمية المطلوبة غير متوفرة.
     */
    public function deductStockForBranch(Product $product, Branch $branch, int $quantity, ?string $notes = 'Sale', ?User $causer = null): StockHistory
    {
        if ($quantity <= 0) {
            throw new Exception('الكمية يجب أن تكون أكبر من صفر.');
        }

        return DB::transaction(function () use ($product, $branch, $quantity, $notes, $causer) {

            // قفل الصف في الجدول الوسيط لمنع الـ Race Conditions.
            $pivotRow = DB::table('branch_product')
                ->where('product_id', $product->id)
                ->where('branch_id', $branch->id)
                ->lockForUpdate()
                ->first();

            $currentQty = $pivotRow->total_quantity ?? 0;

            // التحقق من التوفر بعد قفل الصف.
            if ($currentQty < $quantity) {
                throw new Exception("الكمية المطلوبة للمنتج '{$product->name}' غير متوفرة'.");
            }

            $newQty = $currentQty - $quantity;

            // إنشاء سجل تاريخي، والذي سيقوم بتحديث `total_quantity` تلقائياً.
            return StockHistory::create([
                'product_id' => $product->id,
                'branch_id' => $branch->id,
                'type' => 'decrease',
                'quantity_change' => $quantity,
                'new_quantity' => $newQty,
                'notes' => $notes,
                'user_id' => $causer?->id,
            ]);
        });
    }

    /**
     * التحقق مما إذا كانت كمية معينة من منتج متوفرة في فرع معين.
     *
     * @param Product $product المنتج.
     * @param Branch $branch الفرع.
     * @param int $quantity الكمية المطلوبة.
     * @return bool
     */
    public function isAvailableInBranch(Product $product, Branch $branch, int $quantity): bool
    {
        $pivot = $product->branches()->find($branch->id)?->pivot;
        $currentQty = $pivot->total_quantity ?? 0;

        return $currentQty >= $quantity;
    }

    /**
     * Recalculates and updates the total stock for a specific product in a specific branch.
     *
     * @param Product $product
     * @param Branch $branch
     * @return int The number of affected rows (usually 1 if successful, 0 if not found).
     */
    public function updateStockInBranch(Product $product, Branch $branch): int
    {
        // Step 1: Calculate the correct total from the history table (your logic is correct).
        $total = StockHistory::where('product_id', $product->id)
            ->where('branch_id', $branch->id)
            ->sum(DB::raw('CASE WHEN type = "increase" or type = "initial" THEN quantity_change ELSE -quantity_change END'));

        // Step 2: Update the 'branch_product' pivot table by finding the correct row.
        return DB::table('branch_product')
            ->where('product_id', $product->id) // <-- Find the row using the product's ID
            ->where('branch_id', $branch->id)   // <-- and the branch's ID
            ->update(['total_quantity' => $total]);
    }

    public function updateAll()
    {
        $products = Product::query()
            ->withOutGlobalScope(IsVisibleScope::class)
            // ->with('branches') // لجلب بيانات pivot لكل فرع
            ->get();
        $branch = Filament::getTenant();
        foreach ($products as $product) {
            $this->updateStockInBranch($product, $branch);
        }
    }

    public function updateAllBranches()
    {

        DB::table('branch_product')->update([
            'total_quantity' => DB::raw(
                // Use COALESCE to return 0 if the SUM is NULL
                '(COALESCE((SELECT SUM(CASE WHEN type = "increase" or type = "initial" THEN quantity_change ELSE -quantity_change END)
              FROM stock_histories
              WHERE stock_histories.product_id = branch_product.product_id
              AND stock_histories.branch_id = branch_product.branch_id), 0))'
            )
        ]);
    }
}
