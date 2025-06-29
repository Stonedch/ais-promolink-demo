<?php

declare(strict_types=1);

namespace App\Console\Commands\Converters;

use App\Models\FormResult;
use App\Models\PreparedEvent;
use App\Models\PreparedFormResult;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class PreparedToFormResults extends Command
{
    protected $name = 'prepared-to-form-results:run';
    protected $signature = 'prepared-to-form-results:run {--user-id=} {--event-id=}';

    public function handle(): void
    {
        try {
            $userId = $this->option('user-id');
            $eventId = $this->option('event-id');

            throw_if(empty($userId), new Exception('--user-id= is required'));
            throw_if(empty($eventId), new Exception('--event-id= is required'));

            $preparedEvent = PreparedEvent::where('event_id', $eventId)->first();
            throw_if(empty($preparedEvent), new Exception('Не найдено PreparedEvent для event_id: ' . $eventId));

            $preparedResults = PreparedFormResult::where('prepared_event_id', $preparedEvent->id)->get();

            foreach ($preparedResults as $preparedResult) {
                FormResult::updateOrCreate([
                    'event_id' => $eventId,
                    'field_id' => $preparedResult->field_id,
                    'index' => $preparedResult->index,
                ], [
                    'user_id' => $userId,
                    'value' => $preparedResult->value,
                ]);
            }

            $this->comment('Данные FormResult восстановлены для event_id: ' . $eventId);
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }
}
