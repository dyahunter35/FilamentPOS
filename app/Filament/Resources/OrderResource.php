<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use App\Enums\OrderStatus;
use App\Enums\Payment;
use App\Filament\Forms\Components\DecimalInput;
use App\Filament\Resources\OrderResource\RelationManagers\OrderLogsRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\OrderMetasRelationManager;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Scopes\IsVisibleScope;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static bool $isScopedToTenant = true;
    protected static ?string $recordTitleAttribute = 'number';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('order.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('order.navigation.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('order.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('order.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        // Order details section
                        Section::make(__('order.sections.details.label'))
                            ->icon('heroicon-o-user')
                            ->schema(self::getDetailsFormSchema())
                            ->columns(2),

                        // Order items repeater section
                        Section::make(__('order.sections.order_items.label'))
                            ->icon('heroicon-o-shopping-bag')
                            ->headerActions([
                                Action::make('reset')
                                    ->label(__('order.actions.reset.label'))
                                    ->modalHeading(__('order.actions.reset.modal.heading'))
                                    ->modalDescription(__('order.actions.reset.modal.description'))
                                    ->requiresConfirmation()
                                    ->color('danger')
                                    ->action(fn(Forms\Set $set) => $set('items', [])),
                            ])
                            ->schema([self::getItemsRepeater()]),

                        Section::make(__('order.sections.totals.label'))
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                DecimalInput::make('shipping')
                                    ->label(__('order.fields.shipping.label'))
                                    ->live(onBlur: true)

                                    ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::calculate($get, $set)),
                                DecimalInput::make('install')
                                    ->label(__('order.fields.installation.label'))
                                    ->live(onBlur: true)

                                    ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::calculate($get, $set)),
                                DecimalInput::make('discount')
                                    ->hint(fn($state) => number_format($state))
                                    ->label(__('order.fields.items.discount.label'))
                                    ->disabled()
                                    ->dehydrated(true),
                                DecimalInput::make('total')
                                    ->label(__('order.fields.total.label'))
                                    ->disabled()
                                    ->dehydrated(true),
                                Forms\Components\Textarea::make('notes')
                                    ->label(__('order.fields.notes.label'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->collapsible(),

                    ])
                    ->columnSpan(['lg' => 2]),

                // Status and totals section
                Section::make(__('order.sections.status_and_totals.label'))
                    ->schema(self::getStatusAndTotalsFormSchema())
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('order.fields.created_at.label'))
                    ->date()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('number')
                    ->label(__('order.fields.number.label'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('order.fields.customer.label'))
                    ->searchable()
                    ->formatStateUsing(fn($state, $record) => ($record->is_guest) ? $state . '  ' . __('customer.guest_suffix') : $state)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('order.fields.status.label'))
                    ->badge(),
                Tables\Columns\TextColumn::make('currency')
                    ->label(__('order.fields.currency.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total')
                    ->label(__('order.fields.total.label'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => (string)number_format($state, 2))
                    ->toggleable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money()
                            ->formatStateUsing(fn($state) => (string)number_format($state, 2)),
                    ]),

                Tables\Columns\TextColumn::make('paid')
                    ->label(__('order.fields.paid.label'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => (string)number_format($state, 2))
                    ->toggleable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money()
                            ->formatStateUsing(fn($state) => (string)number_format($state, 2)),
                    ]),

                Tables\Columns\TextColumn::make('shipping')
                    ->label(__('order.fields.shipping.label'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => (string)number_format($state, 2))
                    ->toggleable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money()
                            ->formatStateUsing(fn($state) => (string)number_format($state, 2)),
                    ])->toggleable(isToggledHiddenByDefault: true),



            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->visible(auth()->user()->can('restore_order')),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder('Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(now()->format('M d, Y')),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['created_from'], fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                        ->when($data['created_until'], fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (isset($data['created_from'])) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if (isset($data['created_until'])) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->visible(fn($record) => $record->total != $record->paid || $record->status === OrderStatus::Processing || $record->status === OrderStatus::New)
                    ->requiresConfirmation()
                    ->icon('heroicon-o-credit-card')
                    ->label(__('order.actions.pay.label'))
                    ->modalHeading(__('order.actions.pay.modal.heading'))
                    ->tooltip(__('order.actions.pay.label'))
                    ->iconButton()
                    ->color('info')
                    ->fillForm(fn($record) => [
                        'total' => $record->total,
                        'paid' => $record->paid,
                        'amount' => $record->total - $record->paid,
                    ])
                    ->form([
                        DecimalInput::make('total')
                            ->label(__('order.fields.total.label'))
                            ->disabled(),
                        DecimalInput::make('paid')
                            ->label(__('order.fields.paid.label'))
                            ->disabled(),
                        Forms\Components\Select::make('payment_method')
                            ->label(__('order.fields.payment_method.label'))
                            ->required()
                            ->options(Payment::class)
                            ->default(Payment::Bok),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('order.fields.amount.label'))
                            ->required()
                            ->live(onBlur: true)
                            ->hint(fn($state) => number_format($state))
                            ->hintColor('info')
                            ->numeric()
                            ->rules(['regex:/^-?\d+(\.\d{1,2})?$/'])
                            ->maxValue(fn($record) => $record->total - $record->paid),
                    ])
                    ->action(function (array $data, Order $record) {

                        $record->update([
                            'paid' => $record->paid + $data['amount']
                        ]);

                        $record->orderMetas()->create([
                            'key' => 'payments',
                            'group' => $data['payment_method'] ?? 'cash',
                            'value' => $data['amount']
                        ]);

                        $record->orderLogs()->create([
                            'log' => 'دفع مبلغ ' . number_format($data['amount'], 2) . ' ' . $record->currency . ' بواسطة: ' . auth()->user()->name,
                            'type' => 'payment',
                        ]);

                        if ($record->total === $record->paid) {
                            $record->update(['status' => OrderStatus::Payed]);
                        }

                        Notification::make()
                            ->title(__('order.actions.pay.notification.title'))
                            ->body(__('order.actions.pay.notification.body'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => !$record->deleted_at),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => !$record->deleted_at),
                Tables\Actions\RestoreAction::make()
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->deleted_at && auth()->user()->can('restore_order')),
                Tables\Actions\Action::make('forceDeleteItem')
                    ->label('حذف نهائي')
                    ->requiresConfirmation()
                    ->action(fn(Model $record) => $record->forceDelete())
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->visible(fn($record) => $record->deleted_at && auth()->user()->can('force_delete_order')),
            ])
            ->defaultSort('created_at', 'desc')
            ->groupedBulkActions([
                Tables\Actions\BulkAction::make('forceDelete')
                    ->label('حذف نهائي للمحدد')
                    ->requiresConfirmation()
                    ->action(fn(Collection $records) => $records->each->forceDelete())
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->visible(fn() => auth()->user()->can('force_delete_any_order')),
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation(),
            ])
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderMetasRelationManager::class,
            OrderLogsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}/view'),
            'report' => Pages\SalesReport::route('/report'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'registeredCustomer.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('order.fields.customer.label') => optional($record->registeredCustomer)->name,
            __('order.fields.items.total.label') => number_format($record->total, 0),
            __('order.fields.created_at.label') =>  $record->created_at
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['registeredCustomer', 'items']);
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::$model;
        return (string) $modelClass::where('status', 'new')->where('branch_id', Filament::getTenant()->id)->count();
    }

    // New method to clean up the form's details section
    public static function getDetailsFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('number')
                ->label(__('order.fields.number.label'))
                ->placeholder(__('order.fields.number.placeholder'))
                ->default(Order::generateInvoiceNumber())
                ->readOnly()
                ->dehydrated()
                ->required()
                ->maxLength(32)
                ->unique(Order::class, 'number', ignoreRecord: true),

            Forms\Components\ToggleButtons::make('is_guest')
                ->label(__('order.fields.is_guest.label'))
                ->live()
                ->default(true)
                ->inline()
                ->grouped()
                ->boolean(),

            Forms\Components\Select::make('customer_id')
                ->label(__('order.fields.customer.label'))
                ->placeholder(__('order.fields.customer.placeholder'))
                ->relationship('registeredCustomer', 'name')
                ->searchable()
                ->required(fn(Get $get) => !$get('is_guest'))
                ->preload()
                ->visible(fn(Get $get) => !$get('is_guest'))
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->label(__('customer.fields.name.label'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label(__('customer.fields.email.label'))
                        ->email()
                        ->maxLength(255)
                        ->unique(),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('customer.fields.phone.label'))
                        ->maxLength(255),
                    Forms\Components\Hidden::make('branch_id')
                        ->default(Filament::getTenant()->id),
                ])
                ->createOptionAction(fn(Action $action) => $action
                    ->modalHeading(__('customer.actions.create.modal.heading'))
                    ->modalSubmitActionLabel(__('customer.actions.create.modal.submit'))
                    ->modalWidth('lg')),

            Forms\Components\Section::make(__('order.sections.guest_customer.label'))
                ->schema([
                    Forms\Components\TextInput::make('guest_customer.name')
                        ->label(__('order.fields.guest_customer.name.label'))
                        ->required(fn(Get $get) => $get('is_guest')),
                    Forms\Components\TextInput::make('guest_customer.email')
                        ->label(__('order.fields.guest_customer.email.label'))
                        ->email(),
                    Forms\Components\TextInput::make('guest_customer.phone')
                        ->label(__('order.fields.guest_customer.phone.label'))
                        ->tel()
                        ->prefix('+'),
                ])->columns(3)
                ->visible(fn(Get $get) => $get('is_guest')),
        ];
    }

    // New method to clean up the form's totals section
    public static function getStatusAndTotalsFormSchema(): array
    {
        return [
            Forms\Components\DatePicker::make('created_at')
                ->label(__('order.fields.created_at.label'))
                ->default(now()),
            Forms\Components\ToggleButtons::make('status')
                ->label(__('order.fields.status.label'))
                ->inline()
                ->options(OrderStatus::class)
                ->default(OrderStatus::New)
                ->required(),
            Forms\Components\Select::make('currency')
                ->label(__('order.fields.currency.label'))
                ->searchable()
                ->default('SDG')
                ->options([
                    'SDG' => 'SDG',
                    'USD' => 'USD',
                ])
                ->required(),
        ];
    }

    // New method to clean up the form's repeater logic
    public static function getItemsRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('items')
            ->relationship()
            ->hiddenLabel()
            ->label(__('order.fields.items.label'))
            ->itemLabel(fn(array $state): string => Product::find($state['product_id'])?->name ?? 'Order Item')
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('order.fields.items.product.label'))
                    ->placeholder(__('order.fields.items.product.placeholder'))
                    ->options(
                        Product::whereHas('branches', fn($query) => $query->where('branches.id', Filament::getTenant()->id))
                            ->get()
                            ->mapWithKeys(fn(Product $product) => [
                                $product->id => sprintf(
                                    '%s - %s ($%s) [%s]',
                                    $product->name,
                                    $product->category?->name,
                                    $product->price,
                                    $product->stock_for_current_branch
                                )
                            ])
                    )
                    ->required()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->columnSpan([
                        'lg' => 1,
                        'md' => 2,
                        'sm' => 'full'
                    ])
                    ->searchable(),

                Forms\Components\TextInput::make('description')
                    ->label(__('order.fields.items.description.label'))
                    ->columnSpan([
                        'lg' => 1,
                        'md' => 2,
                        'sm' => 'full'
                    ]),

                Forms\Components\Group::make()
                    ->columns(4)->columnSpanFull()->schema([
                        DecimalInput::make('price')
                            ->label(__('order.fields.items.price.label'))
                            ->columnSpan(1),

                        DecimalInput::make('qty')
                            ->columnSpan(1)
                            ->label(__('order.fields.items.qty.label')),

                        DecimalInput::make('sub_discount')
                            ->label(__('order.fields.items.sub_discount.label'))
                            ->columnSpan(1),

                        DecimalInput::make('sub_total')
                            ->label(__('order.fields.items.sub_total.label'))
                            ->columnSpan(1)
                            ->readOnly()
                            ->dehydrated(true),
                    ])
            ])
            ->live(onBlur: true)
            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::calculate($get, $set))
            ->columns(2)
            ->columnSpanFull();
    }

    public static function calculate(Forms\Get $get, Forms\Set $set): void
    {
        $items = collect($get('items') ?? [])->map(function ($item) {
            $quantity = (float)($item['qty'] ?? 1);
            $unitPrice = (float)($item['price'] ?? 0);
            $itemDiscount = (float)($item['sub_discount'] ?? 0);

            $subTotal = max(0, ($unitPrice - $itemDiscount)) * $quantity;
            $item['sub_total'] = self::truncate_float( $subTotal,2);
            return $item;
        });

        // Update the repeater items
        $set('items', $items->toArray());

        $totalDiscount = $items->sum(fn($item) => (float)($item['sub_discount'] ?? 0) * (float)($item['qty'] ?? 1));
        $totalItemsPrice = $items->sum('sub_total');

        $shipping = (float)($get('shipping') ?? 0);
        $installation = (float)($get('install') ?? 0);

        $set('discount', self::truncate_float( $totalDiscount,2));
        $set('total', self::truncate_float($totalItemsPrice + $installation + $shipping, 2));
    }

    public static function truncate_float(float $number, int $precision): float
    {
        $factor = pow(10, $precision);
        return floor($number * $factor) / $factor;
    }
}
