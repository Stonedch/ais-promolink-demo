<?php

use App\Console\Commands\BotUserFindCommand;
use App\Console\Commands\BotUserNotifyCommand;
use App\Console\Commands\Converters\EventPrepare;
use App\Console\Commands\Converters\FindEventAuthors;
use App\Console\Commands\Converters\SavedStructureConverter;
use App\Console\Commands\CustomReportsCommand;
use App\Console\Commands\ReinitEvents;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command(BotUserFindCommand::class, function () {
    $this->comment('Bot user finding');
})->everyMinute();

Artisan::command(BotUserNotifyCommand::class, function () {
    $this->comment('Bot user notify');
})->everyMinute();

Artisan::command(SavedStructureConverter::class, function () {
    $this->comment('Fix saved structure');
})->hourly();

Artisan::command(FindEventAuthors::class, function () {
    $this->comment('Find event authors');
})->hourly();

Artisan::command(EventPrepare::class, function () {
    $this->comment('Prepare events and results');
})->everyThirtyMinutes();

Artisan::command(ReinitEvents::class, function () {
    $this->comment('Пересоздание переодичных отчетов');
})->hourly();

Artisan::command(CustomReportsCommand::class, function () {
    $this->command('Обработка кастомных отчетов');
})->dailyAt('09:00');
