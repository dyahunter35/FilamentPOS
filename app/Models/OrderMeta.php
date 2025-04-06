<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $invoice_id
 * @property string $key
 * @property mixed $value
 * @property string $type
 * @property string $group
 * @property string $created_at
 * @property string $updated_at
 * @property Invoice $invoice
 */
class OrderMeta extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'key',
        'value',
        'type',
        'group',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'value' => 'json',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
