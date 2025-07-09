<?php

namespace App\Helpers;
namespace App\Services\Forms;

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
use App\Models\FormFieldBlocked;
use App\Models\FormGroup;
use App\Models\FormResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Orchid\Attachment\File;
use Throwable;

ini_set('memory_limit', '-1');

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

            $formResults = FormResult::query()
                ->join('events', 'events.id', 'form_results.event_id')
                ->whereIn('events.form_id', $forms->pluck('id'))
                ->whereIn('events.departament_id', $departaments->pluck('id'))
                ->select(['form_results.id', 'form_results.user_id', 'form_results.event_id', 'form_results.field_id', 'form_results', 'value', 'events.form_id'])
                ->get()
                ->sort()
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
                ->sort()
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

    public static function byDepartamentsRaw(SupportCollection $departaments, bool $arrayReturn = false): array
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

            $formResults = FormResult::query()
                ->join('events', 'events.id', 'form_results.event_id')
                ->whereIn('events.form_id', $forms->pluck('id'))
                ->whereIn('events.departament_id', $departaments->pluck('id'))
                ->select(['form_results.id', 'form_results.user_id', 'form_results.event_id', 'form_results.field_id', 'form_results', 'value', 'events.form_id'])
                ->get()
                ->sort()
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
                ->sort()
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

    public static function reinitResults(
        Event $event,
        array $requestedFields,
        User $user,
        string $savedStructure = '',
        array $files = []
    ): void {
        self::writeResults(
            $event,
            $requestedFields,
            $user,
            $savedStructure,
            $files
        );

        $departament = Departament::find($event->departament_id);
        $parentDepartament = Departament::find($departament->parent_id);

        if (empty($parentDepartament)) {
            $event->filled_at = $event->filled_at ?: now();
            $event->refilled_at = now();
            $event->approval_departament_id = null;
        } else {
            $event->approval_departament_id = $parentDepartament->id;
        }

        if (empty($event->changing_filled_at) == false) {
            $event->filled_at = $event->changing_filled_at;
            $event->refilled_at = $event->changing_filled_at;
        }

        $event->save();
    }

    public static function writeResults(
        Event $event,
        array $requestedFields,
        User $user,
        string $savedStructure = '',
        array $files = []
    ): void {
        FormResult::query()->whereNot('value', '{files}')->where('event_id', $event->id)->delete();

        $structure = json_decode($event->form_structure);

        $blockeds = FormFieldBlocked::query()
            ->where('form_id', $event->form_id)
            ->get()
            ->groupBy('field_id');

        foreach ($structure->fields as $field) {
            try {
                $values = $requestedFields[$field->id];
                if (is_array($values) == false) $values = [$values];
            } catch (Throwable $e) {
                $values = [];
            }

            $fieldBlockeds = $blockeds->get($field->id);

            foreach ($values as $index => $value) {
                $blocked = null;

                if (empty($fieldBlockeds) == false) {
                    $blocked = $fieldBlockeds->where('index', $index)->first();
                }

                if ($value == null) continue;

                $formResultData = [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'field_id' => $field->id,
                    'index' => $index,
                    'value' => $value,
                ];

                if (empty($blocked) == false) {
                    $formResultData['value'] = $blocked->value;
                }

                (new FormResult())->fill($formResultData)->save();
            }
        }

        if (isset($files['fields'])) {
            foreach ($files['fields'] as $fid => $files) {
                foreach ($files as $index => $files) {
                    $formResult = FormResult::query()
                        ->where('index', $index)
                        ->where('field_id', $fid)
                        ->where('event_id', $event->id)
                        ->first();

                    if (empty($formResult)) {
                        $formResult = new FormResult();

                        $formResult->fill([
                            'user_id' => $user->id,
                            'event_id' => $event->id,
                            'field_id' => $fid,
                            'index' => $index,
                            'value' => '{files}',
                        ]);

                        $formResult->save();
                    }

                    $attachments = $formResult->attachment()->pluck('attachments.id');

                    foreach ($files as $file) {
                        $file = new File($file);
                        $attachments[] = $file->load()->id;
                    }

                    $formResult->attachment()->sync($attachments);
                }
            }
        }

        // if (
        //     isset($files['fields'])
        //     && isset($files['fields'][$field->id])
        //     && isset($files['fields'][$field->id][0])
        // ) {
        //     foreach ($files['fields'][$field->id][0] as $file) {
        //         dd($file);
        //     }
        // }

        $event->user_id = $user->id;

        if (empty($savedStructure) == false) {
            $event->saved_structure = $savedStructure;
        }

        $event->save();
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
