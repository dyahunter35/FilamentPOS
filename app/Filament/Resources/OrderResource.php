<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use App\Enums\OrderStatus;
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

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([

                        Forms\Components\Section::make()
                            ->schema(static::getDetailsFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make(__('order.sections.order_items.label'))
                            ->headerActions([
                                Action::make('reset')
                                    ->label(__('order.actions.reset.label'))
                                    ->modalHeading(__('order.actions.reset.modal.heading'))
                                    ->modalDescription(__('order.actions.reset.modal.description'))
                                    ->requiresConfirmation()
                                    ->color('danger')
                                    ->action(fn(Forms\Set $set) => $set('items', [])),
                            ])
                            ->schema(
                                static::getItemsRepeater(),
                            ),
                    ])
                    ->columnSpan(['lg' => fn(?Order $record) => $record === null ? 3 : 2]),

                Forms\Components\Section::make()
                    ->schema([

                        Forms\Components\ToggleButtons::make('status')
                            ->label(__('order.fields.status.label'))
                            ->inline()
                            ->options(OrderStatus::class)
                            ->default(OrderStatus::New)
                            ->required(),

                        Forms\Components\Select::make('currency')
                            ->label(__('order.fields.currency.label'))
                            ->placeholder(__('order.fields.currency.placeholder'))
                            ->searchable()
                            ->default('SDG')
                            ->options([
                                'SDG' => 'SDG',
                                'USD' => 'USD',
                            ])
                            ->required(),

                        Forms\Components\Placeholder::make('created_at')
                            ->label(__('order.fields.created_at.label'))
                            ->content(fn(Order $record): ?string => $record->created_at?->diffForHumans())
                            ->hidden(fn(?Order $record) => $record === null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label(__('order.fields.updated_at.label'))
                            ->content(fn(Order $record): ?string => $record->updated_at?->diffForHumans())
                            ->hidden(fn(?Order $record) => $record === null),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label(__('order.fields.number.label'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('order.fields.customer.label'))
                    ->searchable()
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
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(fn($state) => (string)number_format($state, 2)),
                    ]),
                Tables\Columns\TextColumn::make('paid')
                    ->label(__('order.fields.paid.label')),
                Tables\Columns\TextColumn::make('shipping')
                    ->label(__('order.fields.shipping.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money()
                            ->formatStateUsing(fn($state) => (string)number_format($state, 2)),

                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('order.fields.created_at.label'))
                    ->date()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->visible(auth()->user()->can('restore_order')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->visible(fn($record) => ($record->total != $record->paid) || $record->status === OrderStatus::Processing || $record->status === OrderStatus::New)
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
                        Forms\Components\TextInput::make('total')
                            ->label(__('order.fields.total.label'))
                            ->numeric()
                            ->hint(fn($state) => number_format($state))
                            ->hintColor('info')
                            ->disabled(),
                        Forms\Components\TextInput::make('paid')
                            ->label(__('order.fields.paid.label'))
                            ->numeric()
                            ->hint(fn($state) => number_format($state))
                            ->hintColor('info')
                            ->disabled(),
                        Forms\Components\Select::make('payment_method')
                            ->label(__('order.fields.payment_method.label'))
                            ->required()
                            ->default('bok')
                            ->options([
                                'cash' => __('order.fields.payment_method.options.cash'),
                                'bok' => __('order.fields.payment_method.options.bok'),

                            ]),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('order.fields.amount.label'))
                            ->required()
                            ->live(onBlur:true)
                            ->hint(fn($state) => number_format($state))
                            ->hintColor('info')
                            ->numeric(),
                    ])
                    ->action(function (array $data, Order $record) {

                        if ($data['amount'] > $record->total - $record->paid || $data['amount'] <= 0) {
                            Notification::make()->body('المبلغ غير صحيح الرجاء التأكد')->send();
                            return;
                        }
                        $record->update([
                            'paid' => $record->paid + $data['amount']
                        ]);

                        $record->orderMetas()->create([
                            'key' => 'payments',
                            'group' => $data['payment_method'] ?? 'cash',
                            'value' => $data['amount']
                        ]);

                        $record->orderLogs()->create([
                            'log' => 'Paid ' . number_format($data['amount'], 2) . ' ' . $record->currency . ' By: ' . auth()->user()->name,
                            'type' => 'payment',
                        ]);

                        if ($record->total === $record->paid) {
                            $record->update([
                                'status' => OrderStatus::Payed
                            ]);
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

            ])->defaultSort('created_at', 'desc')
            ->groupedBulkActions([
                Tables\Actions\BulkAction::make('forceDelete')
                    ->label('حذف نهائي للمحدد')
                    ->requiresConfirmation()
                    ->action(fn(Collection $records) => $records->each->forceDelete())
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->visible(fn() => auth()->user()->can('force_delete_any_order')),
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation()

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
            ->withoutGlobalScope(SoftDeletingScope::class)
        ;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'registeredCustomer.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Order $record */

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
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status', 'new')->where('branch_id', Filament::getTenant()->id)->count();
    }

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
                        ->placeholder(__('customer.fields.name.placeholder'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label(__('customer.fields.email.label'))
                        ->placeholder(__('customer.fields.email.placeholder'))
                        ->email()
                        ->maxLength(255)
                        ->unique(),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('customer.fields.phone.label'))
                        ->placeholder(__('customer.fields.phone.placeholder'))
                        ->maxLength(255),
                    Forms\Components\Hidden::make('branch_id')
                        ->default(Filament::getTenant()->id),
                ])
                ->createOptionAction(function (Action $action) {
                    return $action
                        ->modalHeading(__('customer.actions.create.modal.heading'))
                        ->modalSubmitActionLabel(__('customer.actions.create.modal.submit'))
                        ->modalWidth('lg');
                }),

            Forms\Components\Section::make(__('order.sections.guest_customer.label'))
                ->schema([
                    Forms\Components\TextInput::make('guest_customer.name')
                        ->label(__('order.fields.guest_customer.name.label'))
                        ->placeholder(__('order.fields.guest_customer.name.placeholder'))
                        ->required(fn(Get $get) => $get('is_guest')),
                    Forms\Components\TextInput::make('guest_customer.email')
                        ->label(__('order.fields.guest_customer.email.label'))
                        ->placeholder(__('order.fields.guest_customer.email.placeholder'))
                        ->email(),
                    Forms\Components\TextInput::make('guest_customer.phone')
                        ->label(__('order.fields.guest_customer.phone.label'))
                        ->placeholder(__('order.fields.guest_customer.phone.placeholder'))
                        ->tel()
                        ->prefix('+'),
                ])->columns([
                    'lg' => 3,
                    'md' => 2,
                ])
                ->visible(fn(Get $get) => $get('is_guest')),
        ];
    }

    public static function getItemsRepeater()
    {
        return [
            Forms\Components\Repeater::make('items')
                ->relationship() // <-- *** التعديل الأهم هنا ***
                ->hiddenLabel()
                ->label(__('order.fields.items.label'))
                ->itemLabel(__('order.fields.items.item_label'))
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label(__('order.fields.items.product.label'))
                        ->placeholder(__('order.fields.items.product.placeholder'))
                        ->options(
                            Product::whereHas('branches', function ($query) {
                                $query->where('branches.id', Filament::getTenant()->id);
                            })->pluck('name', 'id')
                        )
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            $product = Product::find($state);
                            $set('price', $product?->price ?? 0);
                            $set('description', $product?->description ?? '');
                        })
                        ->distinct()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->columnSpan([
                            'md' => 6,
                        ])
                        ->searchable(),

                    Forms\Components\TextInput::make('description')
                        ->label(__('order.fields.items.description.label'))
                        ->placeholder(__('order.fields.items.description.placeholder'))
                        ->columnSpan(6),
                    Forms\Components\TextInput::make('price')
                        ->label(__('order.fields.items.price.label'))
                        ->placeholder(__('order.fields.items.price.placeholder'))
                        ->columnSpan(3)
                        ->default(0)
                        ->live(onBlur: true)
                        ->numeric(),
                    Forms\Components\TextInput::make('qty')
                        ->live(onBlur: true)
                        ->columnSpan(3)
                        ->label(__('order.fields.items.qty.label'))
                        ->placeholder(__('order.fields.items.qty.placeholder'))
                        ->default(1)
                        ->numeric(),
                    Forms\Components\TextInput::make('sub_discount')
                        ->label(__('order.fields.items.sub_discount.label'))
                        ->placeholder(__('order.fields.items.sub_discount.placeholder'))
                        ->columnSpan(3)
                        ->default(0)
                        ->live(onBlur: true)
                        ->numeric()
                        ->hint(fn(Forms\Get $get) => $get('sub_discount') > $get('price') ? __('order.fields.items.sub_discount.hint_error') : null)
                        ->hintColor('info'),
                    Forms\Components\TextInput::make('sub_total')
                        ->label(__('order.fields.items.sub_total.label'))
                        ->placeholder(__('order.fields.items.sub_total.placeholder'))
                        ->columnSpan(3)
                        ->hint(fn($state) => number_format($state))
                        ->hintColor('success')
                        ->dehydrated(true)
                        ->numeric(),
                ])
                ->live(onBlur: true)
                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                    self::calculate($get, $set);
                })
                ->columns(12)
                ->columnSpanFull(),

            Forms\Components\Section::make(__('order.sections.totals.label'))
                ->schema([
                    Forms\Components\TextInput::make('shipping')
                        ->label(__('order.fields.shipping.label'))
                        ->placeholder(__('order.fields.shipping.placeholder'))
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            self::calculate($get, $set);
                        })
                        ->hint(fn($state) => number_format($state))
                        ->hintColor('info')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    Forms\Components\TextInput::make('discount')
                        ->label(__('order.fields.items.discount.label'))
                        ->placeholder(__('order.fields.items.discount.placeholder'))
                        ->disabled()
                        ->dehydrated(true)
                        ->numeric()
                        ->hint(fn($state) => number_format($state))
                        ->hintColor('info')
                        ->default(0),
                    Forms\Components\TextInput::make('total')
                        ->label(__('order.fields.total.label'))
                        ->placeholder(__('order.fields.total.placeholder'))
                        ->disabled()
                        ->hint(fn($state) => number_format($state))
                        ->hintColor('info')
                        ->dehydrated(true)
                        ->numeric()
                        ->default(0),
                    Forms\Components\Textarea::make('notes')
                        ->label(__('order.fields.notes.label'))
                        ->placeholder(__('order.fields.notes.placeholder'))
                        ->columnSpanFull(),
                ])->columns(4)
                ->collapsible(),
        ];
    }

    public static function calculate(Forms\Get $get, Forms\Set $set)
    {
        $items = $get('items') ?? [];
        $total = 0;
        $discount = 0;
        $recalculatedItems = [];
        foreach ($items as $item) {
            $quantity = (float)($item['qty'] ?? 1);
            $unitPrice = (float)($item['price'] ?? 0);
            $itemDiscount = (float)($item['sub_discount'] ?? 0);

            $subTotal = ($unitPrice - $itemDiscount) * $quantity;
            $item['sub_total'] = $subTotal;

            $total += $subTotal;
            $discount += ($itemDiscount * $quantity);

            $recalculatedItems[] = $item;
        }

        // This is needed to update the sub_total in the UI
        $set('items', $recalculatedItems);

        $shipping = (float)($get('shipping') ?? 0);

        $set('discount', $discount);
        $set('total', $total + $shipping);
    }
}
