<?php

namespace App\Helpers;

use App\Exceptions\HumanException;
use App\Models\Collection;
use App\Models\CollectionValue;
use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\District;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormDepartamentType;
use App\Models\FormGroup;
use App\Models\FormResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;
use Throwable;

class FormHelper
{
    public static array $cachekeys = [
        'byDepartaments' => 'FormHelper.byDepartaments.v0.',
    ];

    public static function byUser(User $user): SupportCollection
    {
        throw_if(empty($user->departament_id), new HumanException('Ошибка проверки пользователя!'));
        $departament = Departament::find($user->departament_id);
        throw_if(empty($departament), new HumanException('Ошибка проверки пользователя!'));
        throw_if(empty($departament->departament_type_id), new HumanException('Ошибка проверки ведомства!'));
        return self::byDepartaments(new SupportCollection([$departament]));
    }

    public static function byDepartaments(SupportCollection $departaments, bool $arrayReturn = false): SupportCollection|array
    {
        try {
            $allEvents = Event::whereIn('departament_id', $departaments->pluck('id'))->get();
            $events = $allEvents->where('filled_at', null);
            $writedEvents = $allEvents->where('filled_at', '!=', null)->keyBy('id')->groupBy('form_id', true);

            $forms = Form::query()
                ->where('is_active', true)
                ->where(function (Builder $query) use ($departaments, $allEvents) {
                    $formIdentifiers = FormDepartamentType::query()
                        ->whereIn('departament_type_id', $departaments->pluck('departament_type_id')->unique())
                        ->pluck('form_id')
                        ->toArray();

                    $formIdentifiers = array_merge($formIdentifiers, $allEvents->pluck('form_id')->toArray());
                    $formIdentifiers = collect($formIdentifiers)->unique();

                    $query->whereIn('id', $formIdentifiers);
                })
                ->get();

            $fields = Field::whereIn('form_id', $forms->pluck('id'))->get();

            $collections = Collection::whereIn('id', $fields->pluck('collection_id'))->get();
            $collectionValues = CollectionValue::whereIn('collection_id', $collections->pluck('id'))->get();

            // dd(FormResult::first());

            $formResults = FormResult::query()
                // ->orderBy('form_results.id', 'DESC')
                ->join('events', 'events.id', 'form_results.event_id')
                ->whereIn('events.form_id', $forms->pluck('id'))
                ->whereIn('events.departament_id', $departaments->pluck('id'))
                ->select(['form_results.id', 'form_results.user_id', 'form_results.event_id', 'form_results.field_id', 'form_results', 'value', 'events.form_id'])
                // ->take(10)
                ->get()
                ->sort()
                ->groupBy(['form_id', 'event_id']);

            // foreach ($formResults as $form => $results) {
            //     $formResults[$form] = $results->sortByDesc('id')->take(1);
            // }

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
                // ->orderBy('id', 'DESC')
                ->whereIn('event_id', $allEvents->pluck('id'))
                // ->take(10)
                ->get()
                ->sort()
                ->groupBy('event_id');

            // foreach ($results as $event => $subresults) {
            //     $results[$event] = $subresults->sortByDesc('id')->take(3);
            // }

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

            $departamentTypes = DepartamentType::whereIn('id', $departaments->pluck('departament_type_id'))->get();
            $districts = District::whereIn('id', $departaments->pluck('district_id'))->get();

            $formCategoryCounters = [];

            $allEvents->map(function (Event $event) use ($formResults) {
                try {
                    $event->maxIndex = $formResults->get($event->form_id)->get($event->id)->max('index');
                } catch (Throwable) {
                }

                return $event;
            });
        } catch (Throwable $e) {
            dd($e);
        }

        $formGroups = FormGroup::whereIn('form_id', $forms->pluck('id'))->orderBy('sort')->get()->groupBy('form_id', true);

        $response = [
            'deadlines' => $deadlines,
            'difs' => $difs,
            'forms' => $forms->keyBy('id'),
            'formCategories' => $formCategories,
            'formGroups' => $formGroups,
            'fields' => $fields->groupBy('form_id'),
            'collections' => $collections,
            'collectionValues' => $collectionValues->groupBy('collection_id'),
            'formResults' => $formResults,
            'events' => $events->keyBy('id'),
            'writedEvents' => $writedEvents,
            'allEvents' => $allEvents->keyBy('id')->groupBy('form_id', true),
            'results' => $results,
            'departaments' => $departaments->keyBy('id'),
            'departamentTypes' => $departamentTypes->keyBy('id'),
            'districts' => $districts,
            'formCategoryCounters' => $formCategoryCounters
       ];

        return $arrayReturn ? $response : collect($response);
    }

    public static function reinitResults(Event $event, array $requestedFields, User $user): void
    {
        self::writeResults($event, $requestedFields, $user);
        $event->filled_at = $event->filled_at ?: now();
        $event->refilled_at = now();
        $event->save();
    }

    public static function writeResults(Event $event, array $requestedFields, User $user, string $savedStructure = ''): void
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
                    'saved_structure' => $savedStructure,
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
