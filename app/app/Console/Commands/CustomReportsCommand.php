<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\CustomReport\CustomReportImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CustomReportsCommand extends Command
{
    protected $name = 'custom-reports:run';
    protected $signature = 'custom-reports:run';
    protected $description = 'Работа с касмотными отчетами';

    public function handle(): void
    {
        $importer = new CustomReportImporter();
        $importer->setConsole($this);
        $importer->setConsoleOutput($this->output);
        $importer->setConsoleInput($this->input);
        $importer->setDebug(false);
        $importer->handle();
    }
}
