<?php

namespace App\Filament\Resources\Categories;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('category.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('category.navigation.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('category.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('category.navigation.group');
    }

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('category.fields.name.label'))
                                    ->placeholder(__('category.fields.name.placeholder'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label(__('category.fields.slug.label'))
                                    ->placeholder(__('category.fields.slug.placeholder'))
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Category::class, 'slug', ignoreRecord: true),
                            ]),

                        Select::make('parent_id')
                            ->label(__('category.fields.parent_id.label'))
                            ->placeholder(__('category.fields.parent_id.placeholder'))
                            ->relationship('parent', 'name', fn (Builder $query) => $query->where('parent_id', null))
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_visible')
                            ->label(__('category.fields.is_visible.label'))
                            ->default(true),

                        MarkdownEditor::make('description')
                            ->label(__('category.fields.description.label'))
                            ->placeholder(__('category.fields.description.placeholder')),
                    ])
                    ->columnSpan(['lg' => fn (?Category $record) => $record === null ? 3 : 2]),
                Section::make()
                    ->schema([
                        Placeholder::make('created_at')
                            ->label(__('category.fields.created_at.label'))
                            ->content(fn (Category $record): ?string => $record->created_at?->diffForHumans()),

                        Placeholder::make('updated_at')
                            ->label(__('category.fields.updated_at.label'))
                            ->content(fn (Category $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Category $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('category.fields.name.label'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.name')
                    ->label(__('category.fields.parent_id.label'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->label(__('category.fields.is_visible.label'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('category.fields.updated_at.label'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make()
                    ->action(function () {
                        Notification::make()
                            ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
