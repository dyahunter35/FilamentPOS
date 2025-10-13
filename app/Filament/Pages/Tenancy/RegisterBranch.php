<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Models\Branch;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterBranch extends RegisterTenant
{
    use HasPageShield;

    public static function getLabel(): string
    {
        return 'Register branch';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([

                    TextInput::make('name')
                        ->afterStateUpdated(function ($state, $set) {
                            $set('slug', Str::slug($state));
                        })->live(onBlur: true),

                    TextInput::make('slug')
                        ->dehydrated()
                        ->readOnly(),
                ])->columnSpan(1)
            ])
            ->columns(1);
    }

    protected function handleRegistration(array $data): Branch
    {
        $branch = Branch::create($data);

        $branch->users()->attach(auth()->user());

        return $branch;
    }

    public static function canAccess(): bool
    {
        // Get the authenticated user
        $user = Auth::user();

        // Return true only if the user's email matches the specific email
        return $user && $user->email === 'dyahunter35@gmail.com';
    }

    public static function canView(): bool
    {
        // Get the authenticated user
        $user = Auth::user();

        // Return true only if the user's email matches the specific email
        return $user && $user->email === 'dyahunter35@gmail.com';
    }
}
