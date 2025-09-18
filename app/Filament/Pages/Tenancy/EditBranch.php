<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Branch;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EditBranch extends EditTenantProfile
{
    use HasPageShield;

    public static function getLabel(): string
    {
        return 'Edit branch';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->afterStateUpdated(function ($state, $set) {
                        $set('slug', Str::slug($state));
                    })->live(onBlur: true),
                TextInput::make('slug')
                    ->dehydrated(),
                Select::make('users')
                    ->relationship('users', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->columns(2);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);


        return $record;
    }
    protected function getRedirectUrl(): ?string
    {
        return route(Filament::getRedirectUrl());
    }

    protected function getRecord(): Model
    {
        return Branch::findOrFail($this->routeParameter('record'));
    }

    public static function canAccess(): bool
    {
        // Get the authenticated user
        $user = Auth::user();

        // Return true only if the user's email matches the specific email
        return $user && $user->email === 'admin@example.com';
    }
}
