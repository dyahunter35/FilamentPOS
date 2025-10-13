<?php

namespace App\Filament\Resources\Products;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
// use App\Filament\Resources\Products\Pages\ProductStockReport;
// use App\Filament\Resources\Products\Pages\BranchReport;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\Products\RelationManagers\HistoryRelationManager;
use App\Models\Product;
use App\Models\Scopes\IsVisibleScope;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bolt';

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
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('product.fields.name.label'))
                                    ->placeholder(__('product.fields.name.placeholder'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, Set $set) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label(__('product.fields.slug.label'))
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                MarkdownEditor::make('description')
                                    ->label(__('product.fields.description.label'))
                                    ->placeholder(__('product.fields.description.placeholder'))
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),


                        Section::make(__('product.sections.pricing.label'))
                            ->schema([
                                TextInput::make('price')
                                    ->label(__('product.fields.price.label'))
                                    ->numeric()
                                    ->required(),


                                /* Forms\Components\TextInput::make('old_price')
                                    ->label(__('product.fields.old_price.label'))
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('cost')
                                    ->label(__('product.fields.cost.label'))
                                    ->helperText(__('product.fields.cost.helper'))
                                    ->numeric()
                                    ->required(), */
                            ])
                            ->columnSpan(1),
                        Section::make(__('product.sections.inventory.label'))
                            ->schema([
                                TextInput::make('security_stock')
                                    ->label(__('product.fields.security_stock.label'))
                                    ->helperText(__('product.fields.security_stock.helper'))
                                    ->numeric()
                                    ->rules(['integer', 'min:0'])
                                    ->required(),

                                /* Forms\Components\TextInput::make('sku')
                                    ->label(__('product.fields.sku.label'))
                                    ->unique(Product::class, 'sku', ignoreRecord: true)
                                    ->required(),

                                Forms\Components\TextInput::make('barcode')
                                    ->label(__('product.fields.barcode.label'))
                                    ->unique(Product::class, 'barcode', ignoreRecord: true)
                                    ->required(), */
                            ])
                            ->columnSpan(1),

                        /* Forms\Components\Section::make(__('product.sections.shipping.label'))
                            ->schema([
                                Forms\Components\Checkbox::make('backorder')
                                    ->label(__('product.fields.backorder.label')),

                                Forms\Components\Checkbox::make('requires_shipping')
                                    ->label(__('product.fields.requires_shipping.label')),
                            ])
                            ->columns(2), */

                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('product.sections.images.label'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('media')
                                    ->collection('product-images')
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->hiddenLabel(),
                            ])
                            ->collapsible(),

                        Section::make(__('product.sections.status.label'))
                            ->schema([
                                Toggle::make('is_visible')
                                    ->label(__('product.fields.is_visible.label'))
                                    ->helperText(__('product.fields.is_visible.helper'))
                                    ->default(true),

                                DatePicker::make('published_at')
                                    ->label(__('product.fields.published_at.label'))
                                    ->default(now())
                                    ->required(),
                            ]),

                        Section::make(__('product.sections.associations.label'))
                            ->schema([
                                Select::make('branches')
                                    ->label(__('product.fields.branch.label'))
                                    ->placeholder(__('product.fields.branch.placeholder'))
                                    ->relationship('branches', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),

                                Select::make('category_id')
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
                SpatieMediaLibraryImageColumn::make('product-image')
                    ->label(__('product.columns.image.label'))
                    ->collection('product-images'),

                TextColumn::make('name')
                    ->label(__('product.columns.name.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__('product.columns.category.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_visible')
                    ->label(__('product.columns.visibility.label'))
                    ->sortable()
                    ->boolean(),

                TextColumn::make('price')
                    ->label(__('product.columns.price.label'))
                    ->searchable()
                    ->sortable(),

                /* Tables\Columns\TextColumn::make('sku')
                    ->label(__('product.columns.sku.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(), */

                TextColumn::make('security_stock')
                    ->label(__('product.columns.security_stock.label'))
                    ->searchable()
                    ->sortable()
                    // ->visible(fn() => !auth()->user()->hasRole('بائع'))
                    ->toggleable(),

                TextColumn::make('stock_for_current_branch')
                    ->label(__('product.columns.quantity.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('total_stock')
                    ->label(__('product.columns.all_branches_quantity.label'))
                    ->searchable()
                    ->sortable()
                    ->color(fn($record) => $record->total_stock > $record->security_stock ? 'success' : 'danger')
                    ->visible(fn() => !auth()->user()->hasRole('بائع'))
                    ->toggleable(),

                TextColumn::make('branches.name')
                    ->label(__('product.columns.branch.label'))
                    ->searchable()
                    ->badge()
                    ->visible(fn() => !auth()->user()->hasRole('بائع'))
                    ->sortable(),

                TextColumn::make('published_at')
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
                        //TextConstraint::make('sku')->label(__('product.filters.constraints.sku')),
                        //TextConstraint::make('barcode')->label(__('product.filters.constraints.barcode')),
                        TextConstraint::make('description')->label(__('product.filters.constraints.description')),
                        //NumberConstraint::make('old_price')->label(__('product.filters.constraints.old_price')),
                        NumberConstraint::make('price')->label(__('product.filters.constraints.price')),
                        // NumberConstraint::make('cost')->label(__('product.filters.constraints.cost')),
                        NumberConstraint::make('security_stock')->label(__('product.filters.constraints.security_stock')),
                        BooleanConstraint::make('is_visible')->label(__('product.filters.constraints.is_visible')),
                        // BooleanConstraint::make('featured')->label(__('product.filters.constraints.featured')),
                        //BooleanConstraint::make('backorder')->label(__('product.filters.constraints.backorder')),
                        //BooleanConstraint::make('requires_shipping')->label(__('product.filters.constraints.requires_shipping')),
                        //DateConstraint::make('published_at')->label(__('product.filters.constraints.published_at')),
                    ])
                    ->constraintPickerColumns(2),
            ], layout: FiltersLayout::Modal)
            ->deferFilters()
            ->recordActions([
                EditAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make()
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
            // 'report' => ProductStockReport::route('/report'),
            // 'branch' => BranchReport::route('/branch-report'),
        ];
    }
}
