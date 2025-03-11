<?php

namespace App\Helpers\EventPrepare;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;

class ChunkEventPreparedHelper extends EventPreparedHelper
{
    public static function chunkPrepare(int $takeByChunk = 100, bool $withPrepared = false, Command $console = null): void
    {
        Event::query()
            ->whereNotNull('filled_at')
            ->where(function (Builder $query) use ($withPrepared) {
                if ($withPrepared == false) {
                    $query->whereNull('prepared_at');
                }
            })
            ->chunk($takeByChunk, function (Collection $events) use ($console, $takeByChunk) {
                $events->map(fn(Event $event) => self::prepare($event));
                if (empty($console) == false) $console->comment(" - ready chunk ({$takeByChunk})");
            });
    }
}
