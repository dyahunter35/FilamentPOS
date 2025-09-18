<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * إضافة كمية جديدة إلى مخزون منتج معين.
     *
     * @param Product $product المنتج الذي ستتم إضافة المخزون إليه.
     * @param int $quantity الكمية المراد إضافتها.
     * @return bool
     */
    public function addStock(Product $product, int $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        // استخدام transaction لضمان تنفيذ العملية بالكامل أو عدم تنفيذها على الإطلاق.
        DB::transaction(function () use ($product, $quantity) {
            $product->increment('qty', $quantity);
        });

        return true;
    }

    /**
     * خصم كمية من مخزون منتج معين بطريقة آمنة لمنع الـ Race Conditions (البيع المتزامن).
     *
     * @param Product $product المنتج الذي سيتم خصم المخزون منه.
     * @param int $quantity الكمية المراد خصمها.
     * @return bool
     * @throws Exception إذا كانت الكمية المطلوبة غير متوفرة.
     */
    public function deductStock(Product $product, int $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        // استخدام transaction لضمان تنفيذ جميع الخطوات معًا.
        return DB::transaction(function () use ($product, $quantity) {
            // lockForUpdate() تقوم بقفل صف المنتج في قاعدة البيانات،
            // مما يمنع أي عمليات أخرى من التعديل عليه حتى انتهاء هذه العملية.
            // هذا يمنع بيع آخر قطعة في المخزون مرتين في نفس اللحظة.
            $product = Product::lockForUpdate()->find($product->id);

            // التحقق من الكمية بعد قفل الصف لضمان دقة البيانات.
            if (!$this->isAvailable($product, $quantity)) {
                throw new Exception("الكمية المطلوبة للمنتج '{$product->name}' غير متوفرة في المخزون.");
            }

            // خصم الكمية من المخزون.
            $product->decrement('qty', $quantity);

            return true;
        });
    }

    /**
     * التحقق مما إذا كانت الكمية المطلوبة متوفرة في المخزون.
     *
     * @param Product $product المنتج.
     * @param int $quantity الكمية المطلوبة.
     * @return bool
     */
    public function isAvailable(Product $product, int $quantity): bool
    {
        return $product->qty >= $quantity;
    }
}
