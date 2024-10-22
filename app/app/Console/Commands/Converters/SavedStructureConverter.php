<?php

declare(strict_types=1);

namespace App\Console\Commands\Converters;

use App\Models\Event;
use App\Models\FormResult;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Throwable;

class SavedStructureConverter extends Command
{
    protected $signature = 'saved-structure:convert';

    public function handle(): void
    {
        Event::query()
            ->with('formResults')
            ->whereHas('formResults')
            ->whereNull('saved_structure')
            ->chunk(10, fn(Collection $events) => $events->map(function (Event $event) {
                try {
                    throw_if(empty($event->form_results));
                    $savedStructure = $event->form_results->first()->saved_structure;
                    throw_if(empty($savedStructure));
                    $event->saved_structure = $savedStructure;
                    $event->save();
                } catch (Throwable | Exception) {
                    return $event;
                }
            }));
    }
}
