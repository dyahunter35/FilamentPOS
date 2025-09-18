<?php

namespace App\Models;

use App\Models\Pivots\BranchProduct;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    protected $fillable = [
        'product_id',
        'brunch_id',
        'type',
        'quantity_change',
        'new_quantity',
        'notes',
        'user_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // This event fires AFTER a new StockHistory record is created.
        static::created(function (StockHistory $history) {
            $history->branch_id = Filament::getTenant()->id;

            // Find the corresponding pivot record
            $pivot = BranchProduct::where('branch_id', $history->branch_id)
                ->where('product_id', $history->product_id)
                ->first();

            if ($pivot) {
                // If the pivot record exists, update its total quantity
                if ($history->type === 'increase') {
                    $pivot->increment('total_quantity', $history->quantity_change);
                } else {
                    $pivot->decrement('total_quantity', $history->quantity_change);
                }
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
