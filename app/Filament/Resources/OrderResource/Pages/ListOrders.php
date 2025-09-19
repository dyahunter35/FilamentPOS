<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return OrderResource::getWidgets();
    }

    public function getTabs(): array
    {
        $tabs = [
            null => Tab::make('All')
                ->label(__('order.fields.status.options.all'))
                ->icon('heroicon-o-rectangle-stack') // Default icon for "All" tab
                ->modifyQueryUsing(function (Builder $query) {
                    // Filter the query based on the status
                    $query->where('branch_id', Filament::getTenant()->id);
                }),
        ];

        // Loop through each case in the ProductStatus Enum
        foreach (OrderStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->value)
                ->label($status->getLabel()) // Use the custom label
                ->icon($status->getIcon()) // Use the custom label
                ->modifyQueryUsing(function (Builder $query) use ($status) {
                    // Filter the query based on the status
                    $query->where('status', $status);
                })
                ->badge(Order::query()->where('status', $status)->where('branch_id', Filament::getTenant()->id)->count()) // Optional: Add a count badge
                ->badgeColor($status->getColor()); // Use a method to get the badge color
        }

        return $tabs;

        return [
            null => Tab::make('All')->label(__('order.fields.status.options.all')),
            'new' => Tab::make()->query(fn($query) => $query->where('status', 'new'))
                ->label(__('order.fields.status.options.new'))
                ->icon('heroicon-o-plus'),
            'processing' => Tab::make()->query(fn($query) => $query->where('status', 'processing'))
                ->label(__('order.fields.status.options.processing'))
                ->icon('heroicon-o-cog'),
            'payed' => Tab::make()->query(fn($query) => $query->where('status', 'payed'))
                ->label(__('order.fields.status.options.payed')),
            'installed' => Tab::make()->query(fn($query) => $query->where('status', 'installed'))
                ->label(__('order.fields.status.options.installed')),
            'delivered' => Tab::make()->query(fn($query) => $query->where('status', 'delivered'))
                ->label(__('order.fields.status.options.delivered')),
            'cancelled' => Tab::make()->query(fn($query) => $query->where('status', 'cancelled'))
                ->label(__('order.fields.status.options.cancelled')),
        ];
    }
}
