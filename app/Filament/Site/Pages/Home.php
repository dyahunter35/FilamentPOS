<?php

namespace App\Filament\Site\Pages;

use Filament\Pages\Page;

class Home extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.site.pages.home'; // 👈 هذا هو مسار الـ Blade view

    protected static ?string $title = 'الرئيسية';

}
