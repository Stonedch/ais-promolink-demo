<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;

class DebugCommand extends Command
{
    protected $name = 'debug:run';
    protected $signature = 'debug:run';
    protected $description = 'This\'s just debug command.';

    // Please clear me after debug
    public function handle(): void
    {
    }
}
