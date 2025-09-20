<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <-- تأكد من استدعاء هذا الكلاس

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// --- أضف جدولة الأوامر الخاصة بك هنا ---
Schedule::command('stock:check-levels')->hourly();
