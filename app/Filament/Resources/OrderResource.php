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
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
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

                        Forms\Components\Section::make('Order items')
                            ->headerActions([
                                Action::make('reset')
                                    ->modalHeading('Are you sure?')
                                    ->modalDescription('All existing items will be removed from the order.')
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
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn(Order $record): ?string => $record->created_at?->diffForHumans())
                            ->hidden(fn(?Order $record) => $record === null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('currency')
                    //->getStateUsing(fn ($record): ?string => Currency::find($record->currency)?->name ?? null)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total')
                    ->searchable()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(),
                    ]),
                Tables\Columns\TextColumn::make('paid'),
                Tables\Columns\TextColumn::make('shipping')
                    ->label('Shipping cost')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(),
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

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
                    ->iconButton()
                    ->color('info')
                    ->fillForm(fn($record) => [
                        'total' => $record->total,
                        'paid' => $record->paid,
                        'amount' => $record->total - $record->paid,
                    ])
                    ->form([
                        Forms\Components\TextInput::make('total')
                            ->label(trans('filament-invoices::messages.invoices.actions.total'))
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('paid')
                            ->label(trans('filament-invoices::messages.invoices.actions.paid'))
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label(trans('filament-invoices::messages.invoices.actions.amount'))
                            ->required()
                            ->numeric(),
                    ])
                    ->action(function (array $data, Order $record) {
                        $record->update([
                            'paid' => $record->paid + $data['amount']
                        ]);

                        $record->orderMetas()->create([
                            'key' => 'payments',
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
                            ->title(trans('filament-invoices::messages.invoices.actions.pay.notification.title'))
                            ->body(trans('filament-invoices::messages.invoices.actions.pay.notification.body'))
                            ->success()
                            ->send();
                    })
                    ->icon('heroicon-o-credit-card')
                    ->label(trans('pay'))
                    ->modalHeading(trans('filament-invoices::messages.invoices.actions.pay.label'))
                    ->tooltip(trans('filament-invoices::messages.invoices.actions.pay.label')),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function () {
                        Notification::make()
                            ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                            ->warning()
                            ->send();
                    }),
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
            OrderLogsRelationManager::class,
            OrderMetasRelationManager::class
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
        ];
    }

    /** @return Builder<Order> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'customer.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Order $record */

        return [
            'Customer' => optional($record->customer)->name,
        ];
    }

    /** @return Builder<Order> */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['customer', 'items']);
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status', 'new')->where('branch_id', Filament::getTenant()->id)->count();
    }

    /** @return Forms\Components\Component[] */
    public static function getDetailsFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('number')
                ->default(Order::generateInvoiceNumber())
                ->disabled()
                ->dehydrated()
                ->required()
                ->maxLength(32)
                ->unique(Order::class, 'number', ignoreRecord: true),

            Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'name')
                ->searchable()
                ->required()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email address')
                        ->required()
                        ->email()
                        ->maxLength(255)
                        ->unique(),

                    Forms\Components\TextInput::make('phone')
                        ->maxLength(255),

                    Forms\Components\Hidden::make('branch_id')
                        ->default(Filament::getTenant()->id),

                    Forms\Components\Select::make('gender')
                        ->placeholder('Select gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                        ])
                        ->required()
                        ->native(false),
                ])
                ->createOptionAction(function (Action $action) {
                    return $action
                        ->modalHeading('Create customer')
                        ->modalSubmitActionLabel('Create customer')
                        ->modalWidth('lg');
                }),

            Forms\Components\ToggleButtons::make('status')
                ->inline()
                ->options(OrderStatus::class)
                ->required(),

            Forms\Components\Select::make('currency')
                ->searchable()
                ->options([
                    'SDG' => 'SDG',
                    'USD' => 'USD',
                ])
                //->getSearchResultsUsing(fn (string $query) => Currency::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
                //->getOptionLabelUsing(fn ($value): ?string => Currency::firstWhere('id', $value)?->getAttribute('name'))
                ->required(),

            /*AddressForm::make('address')
                ->columnSpan('full'),*/

            Forms\Components\MarkdownEditor::make('notes')
                ->columnSpan('full'),
        ];
    }

    public static function getItemsRepeater()
    {
        return [

            Forms\Components\Repeater::make('items')
                ->hiddenLabel()
                ->collapsible()
                ->orderColumn()
                //->collapsed(fn($record) => $record)
                ->cloneable()
                ->relationship('items')
                ->label(trans('filament-invoices::messages.invoices.columns.items'))
                ->itemLabel(trans('filament-invoices::messages.invoices.columns.item'))
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Product')
                        ->options(
                            Product::get()->mapWithKeys(function (Product $product) {
                                return [$product->id => sprintf('%s - %s ($%s)', $product->name, $product->category?->name, $product->price)];
                            })
                        )
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                            $set('price', Product::find($state)?->price ?? 0);
                        })
                        ->distinct()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->columnSpan([
                            'md' => 6,
                        ])
                        ->searchable(),

                    Forms\Components\TextInput::make('description')
                        ->label(trans('filament-invoices::messages.invoices.columns.description'))
                        ->columnSpan(6),
                    Forms\Components\TextInput::make('price')
                        ->label(trans('filament-invoices::messages.invoices.columns.price'))
                        ->columnSpan(3)
                        ->default(0)
                        ->numeric(),
                    Forms\Components\TextInput::make('qty')
                        ->live()
                        ->columnSpan(3)
                        ->label(trans('filament-invoices::messages.invoices.columns.qty'))
                        ->default(1)
                        ->numeric(),
                    Forms\Components\TextInput::make('sub_discount')
                        ->label(trans('filament-invoices::messages.invoices.columns.discount'))
                        ->columnSpan(3)
                        ->default(0)
                        ->numeric()
                        ->hint(fn(Forms\Get $get) => $get('sub_discount') > $get('price') ? 'Discount cannot exceed the price value.' : null)
                        ->hintColor('danger'),

                    Forms\Components\TextInput::make('sub_total')
                        ->label(trans('filament-invoices::messages.invoices.columns.total'))
                        ->columnSpan(3)
                        ->dehydrated(true)
                        ->numeric(),
                ])
                ->lazy()
                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                    self::calculate($get, $set);
                })
                ->columns(12)
                ->columnSpanFull(),
            Forms\Components\Section::make(trans('filament-invoices::messages.invoices.sections.totals.title'))
                ->schema([
                    Forms\Components\TextInput::make('shipping')
                        ->lazy()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            self::calculate($get, $set);
                        })
                        ->label(trans('filament-invoices::messages.invoices.columns.shipping'))
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    Forms\Components\TextInput::make('install')
                        ->label(__('installation'))
                        ->numeric()
                        ->lazy()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            self::calculate($get, $set);
                        })
                        ->default(0),

                    Forms\Components\TextInput::make('discount')
                        ->disabled()
                        ->dehydrated(true)
                        ->label(trans('filament-invoices::messages.invoices.columns.discount'))
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('total')
                        ->disabled()
                        ->dehydrated(true)
                        ->label(trans('filament-invoices::messages.invoices.columns.total'))
                        ->numeric()
                        ->default(0),
                    Forms\Components\Textarea::make('notes')
                        ->label(trans('filament-invoices::messages.invoices.columns.notes'))
                        ->columnSpanFull(),
                ])->columns(4)->collapsible(),
        ];
    }

    public static function calculate(Forms\Get $get, Forms\Set $set)
    {
        $items = $get('items') ?? [];
        $total = 0;
        $discount = 0;
        $collectItems = [];
        foreach ($items as $invoiceItem) {
            $quantity = (float) $invoiceItem['qty'] ?? 0;
            $unitPrice = (float) $invoiceItem['price'] ?? 0;
            $discount_ = (float) $invoiceItem['sub_discount'] ?? 0;
            $getTotal = (($unitPrice  - $discount_) * $quantity);
            $invoiceItem['sub_total'] = $getTotal;

            $total += $getTotal;
            $discount += ($discount_ * $quantity);

            $collectItems[] = $invoiceItem;
        }
        $shipping = (float) $get('shipping') ?? 0;
        $install = (float) $get('install') ?? 0;

        $set('discount', $discount);
        $set('total', $total + $shipping + $install - $discount);

        $set('items', $collectItems);
        //dd($collectItems);
    }

    public static function calculateInRepeter(Forms\Get $get, Forms\Set $set)
    {
        $items = $get('items');
        $total = 0;
        $collectItems = [];

        foreach ($items as $invoiceItem) {
            $quantity = (float) $invoiceItem['qty'] ?? 0;
            $unitPrice = (float) $invoiceItem['price'] ?? 0;
            $discount_ = (float) $invoiceItem['sub_discount'] ?? 0;
            $getTotal = ($unitPrice  - $discount_) * $quantity;
            $invoiceItem['sub_total'] = $getTotal;

            $total += $getTotal;
        }
        $set('items', $collectItems);

        $shipping = (float) $get('shipping') ?? 0;
        $install = (float) $get('install') ?? 0;

        $set('total', $total + $shipping + $install);
    }
}
