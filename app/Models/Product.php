<?php

namespace App\Models;

use App\Models\Pivots\BranchProduct;
use App\Models\Scopes\IsVisibleScope;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new IsVisibleScope());

    }

    /** @return BelonngsTo<Category> */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)
            ->using(BranchProduct::class)
            ->withPivot('total_quantity')
            ->withTimestamps();
    }

    public function history()
    {
        return $this->hasMany(StockHistory::class)->latest();
    }

    /**
     * Get the stock quantity for this product for the CURRENTLY active branch.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function stockForCurrentBranch(): Attribute
    {
        return Attribute::make(
            get: function () {
                // 1. Get the current tenant (the active Branch model)
                $currentBranch = Filament::getTenant();

                // 2. If there is no active branch, return 0
                if (! $currentBranch) {
                    return 0;
                }

                // 3. Find the specific pivot record for this product and the current branch
                $branchPivot = $this->branches()->where('branch_id', $currentBranch->id)->first();

                // 4. Return the 'total_quantity' from the pivot, or 0 if not found
                return $branchPivot?->pivot->total_quantity ?? 0;
            },
        );
    }

    /**
     * Get the total stock quantity for this product across all branches.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function totalStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->branches()->sum('total_quantity'),
        );
    }
}
