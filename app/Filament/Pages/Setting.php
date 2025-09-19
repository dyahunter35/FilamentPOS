<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSetting;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class Setting extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    protected static bool $shouldRegisterNavigation = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }
}
