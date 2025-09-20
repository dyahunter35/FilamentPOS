<?php

namespace App\Models;

use App\Casts\GuestCustomer;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Order extends Model
{
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'total',
        'discount',
        'install',
        'paid',
        'status',
        'currency',
        'shipping',
        'shipping_method',
        'notes',
        'guest_customer',
        'caused_by',
        'branch_id',
        'created_at',
        'updated_at'
    ];


    protected $casts = [
        'status' => OrderStatus::class,
        'guest_customer' => GuestCustomer::class,
        
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return HasMany<OrderItem> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /** @return HasMany<Payment> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function orderLogs()
    {
        return $this->hasMany(OrderLog::class);
    }

     /** @return BelongsTo<Customer,self> */
     public function caused(): BelongsTo
     {
         return $this->belongsTo(User::class,'caused_by');
     }

      /**
     * Define the relationship to a registered customer.
     */
    public function registeredCustomer(): BelongsTo
    {
        // Assuming your registered customer model is 'Customer'
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * This is the magic accessor. It returns the registered customer if one exists,
     * otherwise it returns the temporary guest customer object from the JSON column.
     */
    public function getCustomerAttribute()
    {
        return $this->registeredCustomer ?? $this->guest_customer;
    }

    //
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderMetas()
    {
        return $this->hasMany(OrderMeta::class);
    }

    /**
     * @param string $key
     * @param string|array|object|null $value
     * @return Model|string|array|null
     */
    public function meta(string $key, string|array|object|null $value = null): Model|string|null|array
    {
        if ($value !== null) {
            if ($value === 'null') {
                return $this->orderMetas()->updateOrCreate(['key' => $key], ['value' => null]);
            } else {
                return $this->orderMetas()->updateOrCreate(['key' => $key], ['value' => $value]);
            }
        } else {
            $meta = $this->orderMetas()->where('key', $key)->first();
            if ($meta) {
                return $meta->value;
            } else {
                return $this->orderMetas()->updateOrCreate(['key' => $key], ['value' => null]);
            }
        }
    }

    // دالة لإنشاء رقم فاتورة فريد
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-';
        $year = date('Y');
        $month = date('m');

        $lastInvoice = self::whereYear('created_at', $year)

            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->first();

        $nextNumber = 1;

        if ($lastInvoice) {
            $parts = explode('-', $lastInvoice->number);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . $year . $month . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
