<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\District;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormDepartamentType;
use App\Models\FormResult;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

ini_set('memory_limit', '-1');

class MinisterController extends Controller
{
    public static array $views = [
        'index' => 'web.minister.index',
        'reports' => 'web.minister.reports',
        'by-district' => 'web.minister.by-district',
        'by-departament-type' => 'web.minister.by-departament-type',
        'by-form' => 'web.minister.by-form',
    ];

    public function index(): View|RedirectResponse
    {
        try {
            $this->checkAccess();
            return view(self::$views['index']);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            abort(500);
        }
    }

    public function reports(): View|RedirectResponse
    {
        try {
            $this->checkAccess();

            $forms = Form::query()
                ->select(['forms.*', 'form_categories.name as form_category_name'])
                ->leftJoin('form_categories', 'form_categories.id', '=', 'forms.form_category_id')
                ->where('is_active', true)
                ->get();

            $formEvents = Event::query()
                ->whereIn('form_id', $forms->where('type', 100)->pluck('id'))
                ->whereNotNull('filled_at')
                ->get();

            $forms = $forms->whereIn('id', $formEvents->pluck('form_id'));

            $formDepartamentTypes = FormDepartamentType::query()
                ->select(['form_departament_types.form_id', 'departament_types.name'])
                ->whereIn('form_id', $forms->pluck('id'))
                ->leftJoin('departament_types', 'departament_types.id', '=', 'form_departament_types.departament_type_id')
                ->get();

            $forms->map(function (Form $form) use ($formDepartamentTypes) {
                try {
                    $form->departament_types = $formDepartamentTypes->where('form_id', $form->id);
                } catch (Throwable) {
                } finally {
                    return $form;
                }
            });

            $formResults = FormResult::query()
                ->with('attachment')
                ->whereIn('event_id', $formEvents->pluck('id'))
                ->whereNotNull('value')
                ->where('value', '!=', '')
                ->select(['form_results.id', 'form_results.event_id'])
                ->get()
                ->groupBy('event_id');

            $formEvents->map(function (Event $event) use ($formResults) {
                try {
                    $event->form_structure = json_decode($event->form_structure);
                    $event->filled_percent = $formResults[$event->id]->count() / count($event->form_structure->fields) * 100;
                } catch (Throwable) {
                }

                return $event;
            });

            $forms->map(function (Form $form) use ($formEvents) {
                try {
                    $findedFilledPercents = $formEvents->where('form_id', $form->id)->pluck('filled_percent')->toArray();
                    $form->summary_filled_percent = array_sum($findedFilledPercents) / count($findedFilledPercents);
                } catch (Throwable $e) {
                    dd($formEvents->where('form_id', $form->id), $form);
                    $form->summary_filled_percent = 0;
                }

                return $form;
            });

            $response = [
                'districts' => District::orderBy('name')->whereNot('show_minister_view', false)->get(),
                'departaments' => Departament::all(),
                'departamentTypes' => DepartamentType::where('show_minister_view', true)->orderBy('name')->get(),
                'forms' => $forms,
            ];

            return view(self::$views['reports'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            abort(500);
        }
    }

    public function byForm(?Form $form = null): View|RedirectResponse
    {
        try {
            $this->checkAccess();

            $response = [];

            if (empty($form) == false) {
                throw_if($form->is_active == false, new HumanException('Ошибка обработки формы! Номер ошибки: #1000'));

                $collectionIdentifiers = [];

                $events = Event::query()
                    ->select(['events.*', 'departaments.name as departament_name'])
                    ->leftJoin('departaments', 'departaments.id', '=', 'events.departament_id')
                    ->whereNotNull('filled_at')
                    ->where('form_id', $form->id)
                    ->get()
                    ->map(function (Event $event) use (&$collectionIdentifiers) {
                        $event->form_structure = json_decode($event->form_structure);
                        $event->form_structure->fields = collect($event->form_structure->fields)->keyBy('id')->toArray();

                        $collectionIdentifiers = array_merge(
                            $collectionIdentifiers,
                            collect($event->form_structure->fields)->pluck('collection_id')->whereNotNull()->toArray()
                        );

                        return $event;
                    })
                    ->sortByDesc('created_at');

                $collections = \App\Models\Collection::whereIn('id', $collectionIdentifiers)->get();
                $collectionValues = \App\Models\CollectionValue::whereIn('collection_id', $collections->pluck('id'))->get()->keyBy('id');

                $formResults = FormResult::query()
                    ->with('attachment')
                    ->whereIn('event_id', $events->pluck('id'))
                    ->get();

                $headers = [];

                foreach ($events as $event) {
                    foreach ($event->form_structure->fields as $field) {
                        $headers[$field->id] = (object) [
                            'id' => $field->id,
                            'name' => $field->name,
                            'slug' => \Str::slug($field->name, '-'),
                        ];
                    }
                }

                $response = [
                    'events' => $events,
                    'formResults' => $formResults,
                    'form' => $form,
                    // 'headers' => collect(@$events->sortByDesc('created_at')->first()->form_structure->fields)->sortBy('sort'),
                    'headers' => $headers,
                    'collections' => $collections,
                    'collectionValues' => $collectionValues->groupBy('collection_id', true),
                ];
            } else {
                $forms = Form::query()
                    ->select(['forms.*', 'form_categories.name as form_category_name'])
                    ->leftJoin('form_categories', 'form_categories.id', '=', 'forms.form_category_id')
                    ->where('is_active', true)
                    ->get();

                $formEvents = Event::query()
                    ->whereIn('form_id', $forms->pluck('id'))
                    ->whereNotNull('filled_at')
                    ->get();

                $forms = $forms->whereIn('id', $formEvents->pluck('form_id'));

                $formDepartamentTypes = FormDepartamentType::query()
                    ->select(['form_departament_types.form_id', 'departament_types.name'])
                    ->whereIn('form_departament_types.form_id', $forms->pluck('id'))
                    ->leftJoin('departament_types', 'departament_types.id', '=', 'form_departament_types.departament_type_id')
                    ->get();

                $forms->map(function (Form $form) use ($formDepartamentTypes) {
                    $form->departament_types = $formDepartamentTypes->where('form_id', $form->id);
                });

                $formEvents->map(function (Event $event) {
                    try {
                        $event->form_structure = json_decode($event->form_structure);
                    } catch (Throwable) {
                    }

                    return $event;
                });

                $response = [
                    'forms' => $forms,
                ];
            }

            return view(self::$views['by-form'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            abort(500);
        }
    }

    public function byDistrict(
        District    $district,
        Departament $departament
    ): View|RedirectResponse {
        try {
            $this->checkAccess();

            $response = [
                'district' => $district,
                'departament' => $departament,
            ];

            if ($district->exists && $departament->exists) {
                $responseCollection = FormHelper::byDepartaments(Departament::where('id', $departament->id)->get());

                $response = array_merge(
                    $responseCollection->toArray(),
                    $response
                );

                $departamentTypeEvents = [];
                $departamentTypeEventsCollection = [];

                collect($response['writedEvents'])->map(function ($events, $key) use (&$departamentTypeEvents, &$departamentTypeEventsCollection, $departament) {
                    $finded = collect($events)->where('departament_id', $departament->id);

                    if ($finded->count()) {
                        $departamentTypeEvents[$key] = $finded->keyBy('id')->toArray();
                        $departamentTypeEventsCollection[$key] = $finded->keyBy('id');
                    }
                });

                $departamentTypeEvents = collect($departamentTypeEvents);

                $departamentTypeEventsNotGroupping = [];

                $departamentTypeEvents->map(function ($events) use (&$departamentTypeEventsNotGroupping, $departament) {
                    $departamentTypeEventsNotGroupping = array_merge(
                        $departamentTypeEventsNotGroupping,
                        collect($events)->where('departament_id', $departament->id)->toArray(),
                    );
                });

                $includeForms = collect($response['forms'])->whereIn(
                    'id',
                    collect($departamentTypeEventsNotGroupping)->pluck('form_id'),
                );

                $response['forms'] = $includeForms;
                $response['writedEvents'] = $departamentTypeEvents;
                $response['events'] = $responseCollection['events'];
                $response['allEvents'] = $responseCollection['allEvents'];
            } elseif ($district->exists) {
                $response['districts'] = new Collection();
                $response['departaments'] = Departament::query()
                    ->select(['departaments.*', 'departament_types.sort as departament_type_sort', 'departament_types.name as departament_type_name'])
                    ->leftJoin('departament_types', 'departaments.departament_type_id', '=', 'departament_types.id')
                    ->where('district_id', $district->id)
                    ->orderBy('departament_type_sort')
                    ->get();
            } else {
                $response['districts'] = District::orderBy('name')->whereNot('show_minister_view', false)->get();
                $response['departaments'] = Departament::orderBy('name')->get();
            }

            return view(self::$views['by-district'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            abort(500);
        }
    }


    public function byDepartamentType(
        DepartamentType $departamentType,
        District        $district,
        Departament     $departament
    ): View|RedirectResponse {
        try {
            $this->checkAccess();

            $response = [
                'departamentType' => $departamentType,
                'district' => $district,
                'departament' => $departament,
            ];

            if ($departamentType->exists && $district->exists && $departament->exists) {
                $responseCollection = FormHelper::byDepartaments(Departament::where('id', $departament->id)->get());

                $response = array_merge(
                    $responseCollection->toArray(),
                    $response
                );

                $departamentTypeEvents = [];

                collect($response['writedEvents'])->map(function ($events, $key) use (&$departamentTypeEvents, $departament,) {
                    $finded = collect($events)->where('departament_id', $departament->id);

                    if ($finded->count()) {
                        $departamentTypeEvents[$key] = $finded->keyBy('id')->toArray();
                    }
                });

                $departamentTypeEvents = collect($departamentTypeEvents);

                $departamentTypeEventsNotGroupping = [];

                $departamentTypeEvents->map(function ($events) use (&$departamentTypeEventsNotGroupping, $departament) {
                    $departamentTypeEventsNotGroupping = array_merge(
                        $departamentTypeEventsNotGroupping,
                        collect($events)->where('departament_id', $departament->id)->toArray(),
                    );
                });

                $includeForms = collect($response['forms'])->whereIn(
                    'id',
                    collect($departamentTypeEventsNotGroupping)->pluck('form_id'),
                );

                $response['forms'] = $includeForms;
                $response['writedEvents'] = $departamentTypeEvents;
                $response['events'] = $responseCollection['events'];
                $response['allEvents'] = $responseCollection['allEvents'];
            } elseif ($departamentType->exists && $district->exists) {
                $response['departaments'] = Departament::query()
                    ->where('departament_type_id', $departamentType->id)
                    ->where('district_id', $district->id)
                    ->orderBy('name')
                    ->get();
            } elseif ($departamentType->exists) {
                $response['departaments'] = Departament::where('departament_type_id', $departamentType->id)->orderBy('name')->get();
                $response['districts'] = District::query()->whereIn('id', $response['departaments']->pluck('district_id'))->whereNot('show_minister_view', false)->orderBy('name')->get();
            } else {
                $response['departamentTypes'] = DepartamentType::orderBy('name')->get();
                $response['departaments'] = Departament::orderBy('name')->get();
            }

            return view(self::$views['by-departament-type'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            dd($e);
            abort(500);
        }
    }

    protected function checkAccess(User $user = null): void
    {
        if (empty($user)) $user = Auth::user();
        throw_if(empty($user), new HumanException('Ошибка авторизации!'));
        throw_if($user->hasAnyAccess(['platform.min.base']) == false, new HumanException("Ошибка авторизации!"));
    }
}
