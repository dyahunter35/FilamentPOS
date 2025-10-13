<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StockHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockHistoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockHistory');
    }

    public function view(AuthUser $authUser, StockHistory $stockHistory): bool
    {
        return $authUser->can('View:StockHistory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockHistory');
    }

    public function update(AuthUser $authUser, StockHistory $stockHistory): bool
    {
        return $authUser->can('Update:StockHistory');
    }

    public function delete(AuthUser $authUser, StockHistory $stockHistory): bool
    {
        return $authUser->can('Delete:StockHistory');
    }

    public function restore(AuthUser $authUser, StockHistory $stockHistory): bool
    {
        return $authUser->can('Restore:StockHistory');
    }

    public function forceDelete(AuthUser $authUser, StockHistory $stockHistory): bool
    {
        return $authUser->can('ForceDelete:StockHistory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockHistory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockHistory');
    }

    public function replicate(AuthUser $authUser, StockHistory $stockHistory): bool
    {
        return $authUser->can('Replicate:StockHistory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockHistory');
    }

}