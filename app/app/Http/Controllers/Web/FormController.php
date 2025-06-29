<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionValue;
use App\Models\Departament;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormCheckerResult;
use App\Models\FormGroup;
use App\Models\FormResult;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Throwable;

class FormController extends Controller
{
    protected array $views = [
        'show' => 'web.form.show',
        'preview' => 'web.form.preview',
        'preview-structure' => 'web.form.preview-structure',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        try {
            $user = $request->user();

            throw_if(empty($user), new HumanException('Ошибка авторизации!'));

            throw_if($request->has('id') == false, new HumanException('Ошибка обработки события! #1001'));

            $allEvents = Event::where('id', $request->input('id'))->get()->map(function (Event $event) {
                $event->form_structure = json_decode($event->form_structure, true);
                return $event;
            });

            $events = $allEvents->where('filled_at', null);
            $writedEvents = $allEvents->where('filled_at', '!=', null)->keyBy('id')->groupBy('form_id', true);
            $event = $allEvents->first();

            throw_if(empty($event), new HumanException('Ошибка обработки события! #1002'));

            $forms = Form::where('id', $event->form_id)->get()->keyBy('id');

            $form = $forms->first();

            throw_if(empty($form), new HumanException('Ошибка обработки формы! #1003'));

            $departaments = Departament::find($user->departament_id)->get();
            $departament = $departaments->first();

            $formCategories = FormCategory::whereIn('id', $forms->pluck('form_category_id'))->get()->keyBy('id');
            $formGroups = FormGroup::whereIn('form_id', $forms->pluck('id'))->orderBy('sort')->get()->groupBy('form_id', true);

            $fields = Field::whereIn('form_id', $forms->pluck('id'))->get();

            $collections = Collection::whereIn('id', $fields->pluck('collection_id'))->get();
            $collectionValues = CollectionValue::whereIn('collection_id', $collections->pluck('id'))->get();

            $formResults = FormResult::query()
                ->with('attachment')
                ->join('events', 'events.id', 'form_results.event_id')
                ->whereIn('events.id', $allEvents->pluck('id'))
                ->whereIn('events.form_id', $forms->pluck('id'))
                ->whereIn('events.departament_id', $departaments->pluck('id'))
                ->select([
                    'form_results.id',
                    'form_results.user_id',
                    'form_results.event_id',
                    'form_results.field_id',
                    'form_results.index',
                    'form_results.value',
                    'events.form_id'
                ])
                ->orderByDesc('form_results.id')
                ->get()
                ->map(function (FormResult $result) {
                    $result->saved_structure = json_decode($result->saved_structure, true);
                    $result->form_results = json_decode($result->form_results, true);
                    return $result;
                })
                ->groupBy(['form_id', 'event_id']);

            $allEvents->map(function (Event $event) use ($formResults) {
                if (empty($formResults)) $event;
                if ($formResults->has($event->form_id) == false) return $event;
                if ($formResults->get($event->form_id)->has($event->id) == false) return $event;

                $event->maxIndex = $formResults->get($event->form_id)->get($event->id)->max('index');

                return $event;
            });

            $results = FormResult::query()
                ->whereIn('event_id', $allEvents->pluck('id'))
                ->orderByDesc('form_results.id')
                ->get()
                ->map(function (FormResult $result) {
                    $result->saved_structure = json_decode($result->saved_structure, true);
                    $result->form_results = json_decode($result->form_results, true);
                    return $result;
                })
                ->groupBy('event_id');

            $forms->map(function (Form $form) use ($allEvents, $results) {
                try {
                    $form->last_event = $allEvents->where('form_id', $form->id)->sortByDesc('id')->first();
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

            $response = [
                'form' => $form,
                'departament' => $departament,
                'event' => $event,
                'formCheckerResults' => FormCheckerResult::where('event_id', $event->id)->get(),
                'data' => [
                    'forms' => $forms->toArray(),
                    'formCategories' => $formCategories->toArray(),
                    'formGroups' => $formGroups->toArray(),

                    'fields' => $fields->groupBy('form_id')->toArray(),

                    'collections' => $collections->toArray(),
                    'collectionValues' => $collectionValues->groupBy('collection_id')->toArray(),

                    'events' => $events->keyBy('id')->toArray(),
                    'writedEvents' => $writedEvents->toArray(),
                    'allEvents' => $allEvents->keyBy('id')->groupBy('form_id', true)->toArray(),

                    'formResults' => $formResults->toArray(),
                    'results' => $results->toArray(),
                ],
            ];

            return view($this->views['show'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors(['Внутренняя ошибка']);
        }
    }

    public function preview(Request $request, Departament $departament, Form $form): View|RedirectResponse
    {
        try {
            $user = $request->user();

            $event = Event::query()
                ->where('form_id', $form->id)
                ->where('departament_id', $departament->id);

            if ($request->has('event')) {
                $event = $event->where('id', $request->input('event'))->first();
            } else {
                $event = $event->whereNotNull('filled_at')->orderBy('id', 'desc')->first();
            }

            throw_if(empty($user), new HumanException('Ошибка авторизации! Номер ошибки: #1003.'));

            if ($event->approval_departament_id == request()->user()->departament_id) {
                if ($request->has('approved')) {
                    if ($request->input('approved', null) == 1) {
                        $departament = Departament::find($event->approval_departament_id);
                        $parentDepartament = Departament::find($departament->parent_id);

                        if (empty($parentDepartament)) {
                            $event->filled_at = $event->filled_at ?: now();
                            $event->refilled_at = now();
                            $event->approval_departament_id = null;
                        } else {
                            $event->approval_departament_id = $parentDepartament->id;
                        }

                        $event->save();
                        return redirect()->route('web.index.index');
                    } elseif ($request->input('approved', null) == 0) {
                        $event->approval_departament_id = null;
                        $event->save();
                        return redirect()->route('web.index.index');
                    }
                }
            } else {
                if ($user->hasAnyAccess(['platform.supervisor.base', 'platform.min.base']) == false) {
                    throw_if($user->departament_id != $departament->id, new HumanException('Ошибка авторизации! Номер ошибки: #1004.'));
                }
            }

            throw_if($form->is_active == false, new HumanException('Ошибка обработки формы! Номер ошибки: #1000.'));

            $allEvents = Event::where('id', $event->id)->get()->map(function (Event $event) {
                $event->form_structure = json_decode($event->form_structure, true);
                return $event;
            });

            $events = $allEvents->where('filled_at', null);
            $writedEvents = $allEvents->where('filled_at', '!=', null)->keyBy('id')->groupBy('form_id', true);

            $forms = Form::where('id', $event->form_id)->get()->keyBy('id');

            $departaments = Departament::find($event->departament_id)->get();

            $formCategories = FormCategory::whereIn('id', $forms->pluck('form_category_id'))->get()->keyBy('id');
            $formGroups = FormGroup::whereIn('form_id', $forms->pluck('id'))->orderBy('sort')->get()->groupBy('form_id', true);

            $fields = Field::whereIn('form_id', $forms->pluck('id'))->get();

            $collections = Collection::whereIn('id', $fields->pluck('collection_id'))->get();
            $collectionValues = CollectionValue::whereIn('collection_id', $collections->pluck('id'))->get();

            $formResults = FormResult::query()
                ->with('attachment')
                ->join('events', 'events.id', 'form_results.event_id')
                ->whereIn('events.id', $allEvents->pluck('id'))
                ->whereIn('events.form_id', $forms->pluck('id'))
                ->whereIn('events.departament_id', $departaments->pluck('id'))
                ->select([
                    'form_results.id',
                    'form_results.user_id',
                    'form_results.event_id',
                    'form_results.field_id',
                    'form_results.index',
                    'form_results.value',
                    'events.form_id'
                ])
                ->orderByDesc('form_results.id')
                ->get()
                ->map(function (FormResult $result) {
                    $result->saved_structure = json_decode($result->saved_structure, true);
                    $result->form_results = json_decode($result->form_results, true);
                    return $result;
                })
                ->groupBy(['form_id', 'event_id']);

            $allEvents->map(function (Event $event) use ($formResults) {
                try {
                    $event->maxIndex = $formResults
                        ->get($event->form_id)
                        ->get($event->id)
                        ->max('index');
                } catch (Throwable | Exception) {
                    $event->maxIndex = 0;
                    return $event;
                }

                return $event;
            });

            $results = FormResult::query()
                ->whereIn('event_id', $allEvents->pluck('id'))
                ->orderByDesc('form_results.id')
                ->get()
                ->map(function (FormResult $result) {
                    $result->saved_structure = json_decode($result->saved_structure, true);
                    $result->form_results = json_decode($result->form_results, true);
                    return $result;
                })
                ->groupBy('event_id');

            $forms->map(function (Form $form) use ($allEvents, $results) {
                try {
                    $form->last_event = $allEvents->where('form_id', $form->id)->sortByDesc('id')->first();
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

            $response = [
                'form' => $form,
                'departament' => $departament,
                'event' => $event,
                'formCheckerResults' => FormCheckerResult::where('event_id', $event->id)->get(),
                'data' => [
                    'forms' => $forms->toArray(),
                    'formCategories' => $formCategories->toArray(),
                    'formGroups' => $formGroups->toArray(),

                    'fields' => $fields->groupBy('form_id')->toArray(),

                    'collections' => $collections->toArray(),
                    'collectionValues' => $collectionValues->groupBy('collection_id')->toArray(),

                    'events' => $events->keyBy('id')->toArray(),
                    'writedEvents' => $writedEvents->toArray(),
                    'allEvents' => $allEvents->keyBy('id')->groupBy('form_id', true)->toArray(),

                    'formResults' => $formResults->toArray(),
                    'results' => $results->toArray(),
                ],
            ];

            return view($this->views['preview'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors(['Внутренняя ошибка']);
        }
    }

    public function previewStructure(Request $request, Form $form): View
    {
        $user = $request->user();

        throw_if(empty($user), new HumanException('Ошибка авторизации! Номер ошибки: #1003.'));
        throw_if($user->hasAccess('platform.forms.edit') == false, new HumanException('Ошибка авторизации! Номер ошибки: #1004.'));

        $structure = json_decode($form->getStructure());
        $collections = Collection::whereIn('id', collect($structure->fields)->pluck('collection_id'))->get();
        $collectionValues = CollectionValue::whereIn('collection_id', $collections->pluck('id'))->get();

        $response = [
            'form' => $form,
            'structure' => json_encode($structure, JSON_UNESCAPED_UNICODE),
            'groups' => FormGroup::where('form_id', $form->id)->get(),
            'collections' => $collections->toArray(),
            'collectionValues' => $collectionValues->groupBy('collection_id')->toArray(),
        ];

        return view($this->views['preview-structure'], $response);
    }
}
