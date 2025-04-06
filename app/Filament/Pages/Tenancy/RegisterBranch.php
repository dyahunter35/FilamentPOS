<?php
namespace App\Filament\Pages\Tenancy;

use App\Models\Branch;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

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
                TextInput::make('name'),
                TextInput::make('slug'),
                // ...
            ]);
    }

    protected function handleRegistration(array $data): Branch
    {
        $branch = Branch::create($data);

        $branch->users()->attach(auth()->user());

        return $branch;
    }
}
