<?php

namespace App\Filament\Resources\Customers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static bool $isScopedToTenant = true;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-m-user';
    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('customer.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('customer.navigation.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('customer.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('customer.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([


                        TextInput::make('name')
                            ->label(__('customer.fields.name.label'))
                            ->placeholder(__('customer.fields.name.placeholder'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('customer.fields.email.label'))
                            ->placeholder(__('customer.fields.email.placeholder'))
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label(__('customer.fields.phone.label'))
                            ->placeholder(__('customer.fields.phone.placeholder'))
                            ->tel()
                            ->maxLength(255)
                            ->default(null),

                    ])->columnSpan(2)
                    ->columns(2),
                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->label(__('customer.fields.photo.label'))
                            ->placeholder(__('customer.fields.photo.placeholder'))
                            ->collection('customer_photos'),
                    ])->columnSpan(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('customer_photos')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label(__('customer.fields.name.label'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('customer.fields.email.label'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('customer.fields.phone.label'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('user.fields.created_at.label'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('user.fields.updated_at.label'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->visible(auth()->user()->can('restore_customer')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn($record) => !$record->deleted_at),
                RestoreAction::make()
                    ->visible(fn($record) => $record->deleted_at),
                ForceDeleteAction::make()
                    ->visible(fn($record) => $record->deleted_at),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) Filament::getTenant()->customers->count();
    }
    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
