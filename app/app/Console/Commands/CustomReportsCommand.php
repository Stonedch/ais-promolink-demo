<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CustomReportsCommand extends Command
{
    protected $name = 'custom-reports:run';
    protected $signature = 'custom-reports:run';
    protected $description = 'Работа с касмотными отчетами';

    public function handle(): void
    {
        Artisan::call(config('app.custom_reports_command'));
    }
}
