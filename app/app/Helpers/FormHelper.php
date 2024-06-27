<?php

namespace App\Helpers;

use App\Exceptions\HumanException;
use App\Models\Collection;
use App\Models\CollectionValue;
use App\Models\Departament;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormDepartamentType;
use App\Models\FormResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;
use Throwable;

class FormHelper
{
    public static function byUser(User $user): SupportCollection
    {
        throw_if(empty($user->departament_id), new HumanException('Ошибка проверки пользователя!'));
        $departament = Departament::find($user->departament_id);
        throw_if(empty($departament), new HumanException('Ошибка проверки пользователя!'));
        throw_if(empty($departament->departament_type_id), new HumanException('Ошибка проверки ведомства!'));
        return self::byDepartaments(new SupportCollection([$departament]));
    }

    public static function byDepartaments(SupportCollection $departaments): SupportCollection
    {
        $allEvents = Event::whereIn('departament_id', $departaments->pluck('id'))->get();
        $events = $allEvents->where('filled_at', null);
        $writedEvents = $allEvents->where('filled_at', '!=', null)->keyBy('id')->groupBy('form_id', true);

        $forms = Form::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($departaments, $events) {
                // dd(
                //     $departaments->pluck('departament_type_id')->unique()
                //     // $departaments->pluck('departament_type_id')->toArray()->
                //     // FormDepartamentType::query()->where('departament_type_id', $departaments->pluck('departament_type_id'))->count()
                // );

                $formIdentifiers = FormDepartamentType::query()
                    ->whereIn('departament_type_id', $departaments->pluck('departament_type_id')->unique())
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
            ->whereIn('events.departament_id', $departaments->pluck('id'))
            ->select(['form_results.*', 'events.form_id'])
            ->get()
            ->groupBy(['form_id', 'event_id']);

        $formDeadlines = $forms->pluck('deadline', 'id');
        $deadlines = new SupportCollection();
        $difs = new SupportCollection();

        $events->map(function (Event $event) use ($formDeadlines, &$deadlines, &$difs) {
            $deadline = $formDeadlines->get($event->form_id);
            $deadline = empty($deadline) == false
                ? intval(now()->diff((new Carbon($event->created_at))->addDays($deadline))->format('%d'))
                : null;

            $deadlines->put(
                $event->id,
                $deadline
            );

            $difs->put(
                $event->id,
                now()->diffInSeconds((new Carbon($event->created_at))->addDays($deadline))
            );
        });

        $formCategories = FormCategory::query()
            ->whereIn('id', $forms->pluck('form_category_id'))
            ->get()
            ->keyBy('id');

        $results = FormResult::query()
            ->whereIn('event_id', $allEvents->pluck('id'))
            ->get()
            ->groupBy('event_id');

        $forms->map(function (Form $form) use ($allEvents, $results) {
            try {
                $form->last_event = $allEvents->where('form_id', $form->id)->sortByDesc('id')->first();
                $form->last_event->form_structure = json_decode($form->last_event->form_structure, true);
            } catch (Throwable) {
            }

            try {
                if ($form->type == 100 && empty($form->last_event->filled_at) == false) {
                    $filled = $results[$form->last_event->id]->whereNotNull('value')->count();
                    $needed = count($form->last_event->form_structure['fields']);
                    $form->percent = intval($filled / $needed * 100);
                }
            } catch (Throwable) {
            }

            try {
                $form->form_structure = json_decode($form->getStructure(), true);
            } catch (Throwable) {
            }

            return $form;
        });

        $writedEvents = Event::query()
            ->whereIn('departament_id', $departaments->pluck('id'))
            ->whereNot('filled_at', null)
            ->get()
            ->keyBy('id')
            ->map(function (Event $event) use ($forms, $results) {
                try {
                    $form = $forms->where('id', $event->form_id)->first();
                    if ($form->type == 100 && empty($event->filled_at) == false) {
                        $structure = json_decode($event->form_structure, true);
                        $filled = $results[$event->id]->whereNotNull('value')->unique('field_id')->count();
                        $needed = count($structure['fields']);
                        $event->percent = intval($filled / $needed * 100);
                    }
                } catch (Throwable) {
                }

                return $event;
            })
            ->groupBy('form_id', true);

        return collect([
            'deadlines' => $deadlines,
            'difs' => $difs,
            'forms' => $forms->keyBy('id'),
            'formCategories' => $formCategories,
            'fields' => $fields->groupBy('form_id'),
            'collections' => $collections,
            'collectionValues' => $collectionValues->groupBy('collection_id'),
            'formResults' => $formResults,
            'events' => $events->keyBy('id'),
            'writedEvents' => $writedEvents,
            'allEvents' =>  $allEvents->keyBy('id')->groupBy('form_id', true),
            'results' => $results,
            'departaments' => $departaments->keyBy('id'),
        ]);
    }

    public static function reinitResults(Event $event, array $requestedFields, User $user): void
    {
        self::writeResults($event, $requestedFields, $user);
        $event->filled_at = $event->filled_at ?: now();
        $event->refilled_at = now();
        $event->save();
    }

    public static function writeResults(Event $event, array $requestedFields, User $user): void
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
    }

    public static function getPercent(Event $event): int
    {
        try {
            $structure = json_decode($event->form_structure);
            $results = FormResult::query()->where('event_id', $event->id)->get();
            $filled = $results->whereNotNull('value')->count();
            $needed = count($structure->fields);
            return intval($filled / $needed * 100);
        } catch (Throwable) {
            return 0;
        }
    }
}
