<?php

namespace App\Filament\Site\Pages;

use Filament\Pages\Page;

class Locations extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.site.pages.locations';
}
