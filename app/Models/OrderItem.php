<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PhpParser\Node\Expr\Cast\Double;

class OrderItem extends Model
{
    protected $guarded = [];

    /** @return BelongsTo<Product,self> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
