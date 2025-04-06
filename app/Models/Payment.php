<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $guarded = [];

    /** @return BelongsTo<Order,self> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
