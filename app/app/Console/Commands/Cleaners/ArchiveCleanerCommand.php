<?php

declare(strict_types=1);

namespace App\Console\Commands\Cleaners;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ArchiveCleanerCommand extends Command
{
    protected $signature = 'cleaners:archives:clean';

    public function handle(): void
    {
        Artisan::call('cleaners:folder:clean', [
            'disk' => 'private',
            'path' => 'archives',
        ]);
    }
}
