<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Debug extends Command
{
    protected $name = 'app:debug:run';
    protected $signature = 'app:debug:run';
    protected $description = 'This\'s just debug command.';

    // Please clear me after debug
    public function handle(): void
    {
    }
}
