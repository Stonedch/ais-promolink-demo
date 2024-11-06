<?php

declare(strict_types=1);

namespace App\Console\Commands\Converters;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class FindEventAuthors extends Command
{
    protected $name = 'find-event-authors:run';
    protected $signature = 'find-event-authors:run';

    public function handle(): void
    {
        Event::query()
            ->with('formResults')
            ->whereNotNull('filled_at')
            ->whereNull('user_id')
            ->chunk(100, function (Collection $events) {
                foreach ($events as $event) {
                    $firstFormResult = $event->formResults->first();
                    $event->user_id = $firstFormResult->user_id;
                    $event->save();
                    $this->comment("id:{$event->id}; user_id:{$event->user_id}");
                }
            });
    }
}
