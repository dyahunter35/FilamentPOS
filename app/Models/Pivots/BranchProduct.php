<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot; // <-- 1. This is the CORRECT use statement
use Illuminate\Support\Facades\DB;
use App\Models\StockHistory;

//                                               <-- 2. Make sure it extends Pivot
class BranchProduct extends Pivot
{
    /**
     * The table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'branch_product'; // <-- 3. Ensure the table name is correct

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (BranchProduct $pivot) {
            $total = StockHistory::where('product_id', $pivot->product_id)
                                 ->where('branch_id', $pivot->branch_id)
                                 ->sum(DB::raw('CASE WHEN type = "increase" THEN quantity_change ELSE -quantity_change END'));

            DB::table('branch_product')->where('id', $pivot->id)->update(['total_quantity' => $total]);

           // \App\Models\StockHistory::where('product_id', 1) ->where('branch_id',1) ->sum(DB::raw('CASE WHEN type = "increase" or type="initalize" THEN quantity_change ELSE -quantity_change END'));
        });

    }
}
