<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CustomReportData;
use Illuminate\Console\Command;

class DebugCommand extends Command
{
    protected $name = 'debug:run';
    protected $signature = 'debug:run';
    protected $description = 'This\'s just debug command.';

    // Please clear me after debug
    public function handle(): void
    {
        dd(CustomReportData::count());
    }
}
