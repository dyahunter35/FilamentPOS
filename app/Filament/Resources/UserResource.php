<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';
    protected static ?int $navigationSort = 1;
    protected static bool $isScopedToTenant = true;

    public static function getModelLabel(): string
    {
        return __('user.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user.navigation.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('user.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('user.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('user.sections.general'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('user.fields.name.label'))
                            ->placeholder(__('user.fields.name.placeholder'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('user.fields.email.label'))
                            ->placeholder(__('user.fields.email.placeholder'))
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label(__('user.fields.password.label'))
                            ->placeholder(__('user.fields.password.placeholder'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn($state): bool => filled($state))
                            ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                            ->live(debounce: 500),
                    ])->columnSpan(2)
                    ->columns(2),
                Forms\Components\Section::make(__('user.sections.roles'))
                    ->schema([

                        Forms\Components\Select::make('roles')
                            ->label(__('user.fields.roles.label'))
                            ->placeholder(__('user.fields.roles.placeholder'))
                            ->relationship('roles', 'name')
                            ->saveRelationshipsUsing(function (Model $record, $state) {
                                $record->roles()->sync($state);
                            })
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Forms\Components\Select::make('branch')
                            ->label(__('user.fields.branch.label'))
                            ->placeholder(__('user.fields.branch.placeholder'))
                            ->relationship('branch', 'name')
                            ->saveRelationshipsUsing(function (Model $record, $state) {
                                $record->branch()->sync($state);
                            })
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('user.fields.name.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('user.fields.email.label'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('user.fields.roles.label'))
                    ->searchable()
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label(__('user.fields.branch.label'))
                    ->searchable()
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('user.fields.created_at.label'))

                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('user.fields.updated_at.label'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->hidden(fn(User $user) => (auth()->user()->hasRole('super_admin') && $user->id == auth()->user()->id)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
