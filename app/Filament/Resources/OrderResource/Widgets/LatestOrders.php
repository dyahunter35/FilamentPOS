<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
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
                Tables\Columns\TextColumn::make('number')
                    ->label('رقم الطلب'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('العميل'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                Tables\Columns\TextColumn::make('total')
                    ->label('الإجمالي')
                    ->money('SDG', true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('عرض')
                    ->url(fn($record): string => OrderResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
