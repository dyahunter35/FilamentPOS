<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Branch;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterBranch extends RegisterTenant
{
    use HasPageShield;

    public static function getLabel(): string
    {
        return 'Register branch';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
        return true; //$user && $user->email === 'admin@example.com';
    }
}
