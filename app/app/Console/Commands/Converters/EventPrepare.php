<?php

declare(strict_types=1);

namespace App\Console\Commands\Converters;

use Illuminate\Console\Command;

class EventPrepare extends Command
{
    protected $name = 'event-prepare:run';
    protected $signature = 'event-prepare:run';

    public function handle(): void
    {
        ChunkEventPreparedHelper::chunkPrepare(
            takeByChunk: 100,
            withPrepared: false
        );
    }
}
