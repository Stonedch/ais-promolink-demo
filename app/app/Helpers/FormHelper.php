<?php

namespace App\Helpers;

use App\Exceptions\HumanException;
use App\Models\Collection;
use App\Models\CollectionValue;
use App\Models\Departament;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormDepartamentType;
use App\Models\FormResult;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Throwable;

class FormHelper
{
    public static function byUser(User $user): \Illuminate\Support\Collection
    {
        throw_if(empty($user->departament_id), new HumanException('Ошибка проверки пользователя!'));

        $departament = Departament::find($user->departament_id);

        $events = Event::query()
            ->where('departament_id', $departament->id)
            ->where('filled_at', null)
            ->get();

        throw_if(empty($departament), new HumanException('Ошибка проверки пользователя!'));
        throw_if(empty($departament->departament_type_id), new HumanException('Ошибка проверки ведомства!'));

        $forms = Form::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($departament, $events) {
                $formIdentifiers = FormDepartamentType::query()
                    ->where('departament_type_id', $departament->departament_type_id)
                    ->pluck('form_id')
                    ->toArray();

                $formIdentifiers = array_merge($formIdentifiers, $events->pluck('form_id')->toArray());
                $formIdentifiers = collect($formIdentifiers)->unique();

                $query->whereIn('id', $formIdentifiers);
            })
            ->get();

        $fields = Field::whereIn('form_id', $forms->pluck('id'))->get();

        $collections = Collection::whereIn('id', $fields->pluck('collection_id'))->get();

        $collectionValues = CollectionValue::whereIn('collection_id', $collections->pluck('id'))->get();

        $formResults = FormResult::query()
            ->join('events', 'events.id', 'form_results.event_id')
            ->whereIn('events.form_id', $forms->pluck('id'))
            ->where('events.departament_id', $departament->id)
            ->select(['form_results.*', 'events.form_id'])
            ->get()
            ->groupBy(['form_id', 'event_id']);

        return collect([
            'forms' => $forms,
            'fields' => $fields->groupBy('form_id'),
            'collections' => $collections,
            'collectionValues' => $collectionValues->groupBy('collection_id'),
            'events' => $events,
            'formResults' => $formResults,
        ]);
    }

    public static function reinitResults(Event $event, array $requestedFields, User $user): void
    {
        FormResult::query()->where('event_id', $event->id)->delete();
        $structure = json_decode($event->form_structure);

        foreach ($structure->fields as $field) {
            try {
                $values = $requestedFields[$field->id];
                if (is_array($values) == false) $values = [$values];
            } catch (Throwable $e) {
                $values = [];
            }

            foreach ($values as $index => $value) {
                (new FormResult())->fill([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'field_id' => $field->id,
                    'index' => $index,
                    'value' => $value,
                ])->save();
            }
        }

        $event->filled_at = $event->filled_at ?: now();
        $event->refilled_at = now();
        $event->save();
    }
}
