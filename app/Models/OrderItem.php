<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $guarded = [];

    /** @return BelongsTo<Product,self> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)
            /* ->whereHas('branches', function ($query) {
                $query->where('branches.id', Filament::getTenant()->id);
            }) */;
    }
}
