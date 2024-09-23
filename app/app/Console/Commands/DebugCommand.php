<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CustomReport;
use App\Models\Event;
use App\Models\Field;
use App\Models\FormResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DebugCommand extends Command
{
    protected $name = 'debug:run';
    protected $signature = 'debug:run';
    protected $description = 'This\'s just debug command.';

    // Please clear me after debug
    public function handle(): void
    {
        dd(
            FormResult::where('event_id', 3012)->first()->saved_structure
        );
    }
}
