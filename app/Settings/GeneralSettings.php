<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public $site_name;

    public static function group(): string
    {
        return 'general';
    }


}
