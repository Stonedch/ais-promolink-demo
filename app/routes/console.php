<?php

use App\Console\Commands\BotUserFindCommand;
use App\Console\Commands\BotUserNotifyCommand;
use App\Console\Commands\Temporary\WorkWithCustomReportsCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command(BotUserFindCommand::class, function () {
    $this->comment("Bot user finding");
})->everyMinute();

Artisan::command(BotUserNotifyCommand::class, function () {
    $this->comment("Bot user notify");
})->everyMinute();

Artisan::command(WorkWithCustomReportsCommand::class, function () {
    $this->comment("Work With Custom Reports Command");
})->everyMinute();
