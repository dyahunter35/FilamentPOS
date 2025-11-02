<?php

namespace App\Filament\Resources\Orders\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'أحدث الطلبات';

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery()->latest()->limit(10)) // جلب آخر 10 طلبات
            ->columns([
                TextColumn::make('number')
                    ->label('رقم الطلب'),
                TextColumn::make('customer.name')
                    ->label('العميل'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                TextColumn::make('total')
                    ->label('الإجمالي')
                    ->money('SDG', true),
                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('عرض')
                    ->url(fn($record): string => OrderResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
