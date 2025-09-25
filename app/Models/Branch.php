<?php

namespace App\Models;

use App\Models\Pivots\BranchProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
                ->using(BranchProduct::class)
                ->withPivot('total_quantity')
                ->withTimestamps();

    }


    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }


    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'team_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function stock_histories(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }


    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }
}
