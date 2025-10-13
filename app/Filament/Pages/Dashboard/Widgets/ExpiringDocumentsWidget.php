<?php

namespace App\Filament\Pages\Dashboard\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Models\VendorFile;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ExpiringDocumentsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                VendorFile::query()
                    ->whereNotNull('expiry_date')
                    ->where('expiry_date', '>=', now())
                    ->where('expiry_date', '<=', now()->addDays(30))
                    ->with('vendor')
                    ->latest('expiry_date')
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('dashboard.table.document_name'))
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label(__('dashboard.vendor'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('dashboard.table.document_type')),
                TextColumn::make('expiry_date')
                    ->label(__('dashboard.expires'))
                    ->date()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $daysLeft = Carbon::parse($state)->diffInDays(now());
                        return Carbon::parse($state)->format('Y-m-d') . ' (' . __('dashboard.stats.days_left', ['days' => $daysLeft]) . ')';
                    }),
                TextColumn::make('status')
                    ->label(__('dashboard.table.status'))
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('view')
                    ->label(__('dashboard.actions.view'))
                    ->url(fn (VendorFile $record): string => route('filament.admin.resources.vendors.edit', $record->vendor_id))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-eye'),
            ])
            ->toolbarActions([
                //
            ])
            ->emptyStateHeading(__('dashboard.alerts.no_expiring_documents'))
            ->emptyStateIcon('heroicon-o-document-check')
            ->heading(__('dashboard.widgets.expiring_documents'));
    }
}
