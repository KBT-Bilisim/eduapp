<?php

use App\Jobs\HelloWorldJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Production: Her 5 dakikada bir HelloWorldJob
Schedule::job(new HelloWorldJob)->everyMinute();

// Queue worker'ı her dakika çalıştır (pending job'ları işlemek için)
Schedule::command('queue:work --stop-when-empty --timeout=60 --tries=3')
    ->everyMinute()
    ->runInBackground();
