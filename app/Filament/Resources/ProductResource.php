<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\HistoryRelationManager;
use App\Models\Product;
use App\Models\Scopes\IsVisibleScope;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 3;

    // --- NAVIGATION ---
    public static function getModelLabel(): string
    {
        return __('product.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('product.navigation.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('product.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('product.navigation.group');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(IsVisibleScope::class);
    }

    // --- FORM ---
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('product.fields.name.label'))
                                    ->placeholder(__('product.fields.name.placeholder'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $set('slug', Str::slug($state))),

                                Forms\Components\TextInput::make('slug')
                                    ->label(__('product.fields.slug.label'))
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('description')
                                    ->label(__('product.fields.description.label'))
                                    ->placeholder(__('product.fields.description.placeholder'))
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),


                        Forms\Components\Section::make(__('product.sections.pricing.label'))
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label(__('product.fields.price.label'))
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('old_price')
                                    ->label(__('product.fields.old_price.label'))
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('cost')
                                    ->label(__('product.fields.cost.label'))
                                    ->helperText(__('product.fields.cost.helper'))
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make(__('product.sections.inventory.label'))
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->label(__('product.fields.sku.label'))
                                    ->unique(Product::class, 'sku', ignoreRecord: true)
                                    ->required(),

                                Forms\Components\TextInput::make('barcode')
                                    ->label(__('product.fields.barcode.label'))
                                    ->unique(Product::class, 'barcode', ignoreRecord: true)
                                    ->required(),

                                Forms\Components\TextInput::make('security_stock')
                                    ->label(__('product.fields.security_stock.label'))
                                    ->helperText(__('product.fields.security_stock.helper'))
                                    ->numeric()
                                    ->rules(['integer', 'min:0'])
                                    ->required(),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make(__('product.sections.shipping.label'))
                            ->schema([
                                Forms\Components\Checkbox::make('backorder')
                                    ->label(__('product.fields.backorder.label')),

                                Forms\Components\Checkbox::make('requires_shipping')
                                    ->label(__('product.fields.requires_shipping.label')),
                            ])
                            ->columns(2),

                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('product.sections.images.label'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('media')
                                    ->collection('product-images')
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->hiddenLabel(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make(__('product.sections.status.label'))
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label(__('product.fields.is_visible.label'))
                                    ->helperText(__('product.fields.is_visible.helper'))
                                    ->default(true),

                                Forms\Components\DatePicker::make('published_at')
                                    ->label(__('product.fields.published_at.label'))
                                    ->default(now())
                                    ->required(),
                            ]),

                        Forms\Components\Section::make(__('product.sections.associations.label'))
                            ->schema([
                                Forms\Components\Select::make('branches')
                                    ->label(__('product.fields.branch.label'))
                                    ->placeholder(__('product.fields.branch.placeholder'))
                                    ->relationship('branches', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Select::make('category_id')
                                    ->label(__('product.fields.category.label'))
                                    ->placeholder(__('product.fields.category.placeholder'))
                                    ->relationship('category', 'name')
                                    ->preload()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    // --- TABLE ---
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product-image')
                    ->label(__('product.columns.image.label'))
                    ->collection('product-images'),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('product.columns.name.label'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('product.columns.category.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label(__('product.columns.visibility.label'))
                    ->sortable()
                    ->boolean(),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('product.columns.price.label'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label(__('product.columns.sku.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('stock_for_current_branch')
                    ->label(__('product.columns.quantity.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_stock')
                    ->label(__('product.columns.quantity.label'))
                    ->searchable()
                    ->sortable()
                    ->visible(fn() => auth()->user()->hasRole('super_admin'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(__('product.columns.branch.label'))
                    ->searchable()
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('security_stock')
                    ->label(__('product.columns.security_stock.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('product.columns.publish_date.label'))
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('name')->label(__('product.filters.constraints.name')),
                        TextConstraint::make('slug')->label(__('product.filters.constraints.slug')),
                        TextConstraint::make('sku')->label(__('product.filters.constraints.sku')),
                        TextConstraint::make('barcode')->label(__('product.filters.constraints.barcode')),
                        TextConstraint::make('description')->label(__('product.filters.constraints.description')),
                        NumberConstraint::make('old_price')->label(__('product.filters.constraints.old_price')),
                        NumberConstraint::make('price')->label(__('product.filters.constraints.price')),
                        NumberConstraint::make('cost')->label(__('product.filters.constraints.cost')),
                        NumberConstraint::make('security_stock')->label(__('product.filters.constraints.security_stock')),
                        BooleanConstraint::make('is_visible')->label(__('product.filters.constraints.is_visible')),
                        BooleanConstraint::make('featured')->label(__('product.filters.constraints.featured')),
                        BooleanConstraint::make('backorder')->label(__('product.filters.constraints.backorder')),
                        BooleanConstraint::make('requires_shipping')->label(__('product.filters.constraints.requires_shipping')),
                        DateConstraint::make('published_at')->label(__('product.filters.constraints.published_at')),
                    ])
                    ->constraintPickerColumns(2),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->deferFilters()
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function () {
                        Notification::make()
                            ->title(__('product.actions.delete.notification'))
                            ->warning()
                            ->send();
                    }),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            HistoryRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'report' => Pages\ProductStockReport::route('/{record}/report'),
        ];
    }
}
