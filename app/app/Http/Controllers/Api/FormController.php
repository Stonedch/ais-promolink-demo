<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormFieldBlocked;
use App\Models\FormResult;
use App\Orchid\Components\HumanizePhone;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class FormController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            throw_if(empty($user));

            if ($user->hasAnyAccess(['platform.supervisor.base', 'platform.min.base'])) {
                $response = FormHelper::byDepartaments(Departament::whereNotNull('departament_type_id')->get());
            } else {
                $response = FormHelper::byUser($user);
            }

            return Responser::returnSuccess($response->toArray());
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError([$e->getMessage()]);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $response = [];

            $user = $request->user();
            throw_if(empty($user));
            throw_if(empty($user->departament_id), new HumanException('600, Ошибка проверки пользователя!'));
            throw_if($user->hasAnyAccess(['platform.departament-director.base']), new HumanException('604, Ошибка проверки пользователя!'));

            $event = Event::find($request->input('event_id', null));
            throw_if(empty($event), new HumanException('602, Ошибка проверки формы!'));
            throw_if($event->departament_id != $user->departament_id, new HumanException('603, Ошибка проверки пользователя!'));
            throw_if(empty($event->filled_at) == false, new HumanException('604, Ошибка проверки формы!'));

            FormHelper::reinitResults($event, $request->input('fields', []), $user, $request->input('structure', ''));

            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError([$e->getMessage()]);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        try {
            $response = [];

            $user = $request->user();
            throw_if(empty($user));
            throw_if(empty($user->departament_id), new HumanException('Ошибка проверки пользователя!'));
            throw_if($user->hasAnyAccess(['platform.departament-director.base']), new HumanException('604, Ошибка проверки пользователя!'));

            $form = Form::find($request->input('form_id', null));
            throw_if(empty($form), new HumanException('Ошибка проверки формы!'));
            throw_if($form->is_editable != true, new HumanException('Ошибка проверки формы!'));

            $event = Event::orderBy('id', 'desc')->where('form_id', $form->id)->where('filled_at', '<>', null)->first();
            throw_if(empty($event), new HumanException('Ошибка проверки формы!'));
            throw_if($event->departament_id != $user->departament_id, new HumanException('Ошибка проверки пользователя!'));

            FormHelper::reinitResults($event, $request->input('fields', []), $user);

            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError();
        }
    }

    public function saveDraft(Request $request): JsonResponse
    {
        try {
            $response = [];

            $user = $request->user();
            throw_if(empty($user));
            throw_if(empty($user->departament_id), new HumanException('600, Ошибка проверки пользователя!'));
            throw_if($user->hasAnyAccess(['platform.departament-director.base']), new HumanException('604, Ошибка проверки пользователя!'));

            $event = Event::find($request->input('event_id', null));
            throw_if(empty($event), new HumanException('601, Ошибка проверки формы!'));
            throw_if($event->departament_id != $user->departament_id, new HumanException('602, Ошибка проверки пользователя!'));
            throw_if(
                $user->hasAccess('platform.forms.admin-edit') == false && empty($event->filled_at) == false,
                new HumanException('603, Ошибка проверки формы!')
            );

            throw_if($event->getCurrentStatus() == 200, new HumanException('604, Ошибка проверки формы, просрочено!'));

            if ($request->input('json', false)) {
                $fields = $request->input('fields', []);

                foreach ($fields as $key => $value) {
                    $fields[$key] = json_decode($value[0], true);
                }

                $request->merge(['fields' => $fields]);
            }

            FormHelper::writeResults(
                $event,
                $request->input('fields', []),
                $user,
                $request->input('structure', ''),
                files: $request->file()
            );

            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError([$e->getMessage()]);
        }
    }

    public function getOldValues(Request $request): JsonResponse
    {
        try {
            $response = [];

            $user = $request->user();
            throw_if(empty($user), new HumanException('Ошибка проверки пользователя', 100));
            throw_if(empty($user->departament_id), new HumanException('Ошибка проверки учреждения', 110));

            $event = Event::find($request->input('event_id', null));
            throw_if(empty($event), new HumanException('Ошибка проверки формы', 120));
            throw_if($event->departament_id != $user->departament_id, new HumanException('Ошибка проверки учреждения', 130));

            $oldEvent = Event::query()
                ->orderBy('id', 'desc')
                ->whereNot('id', $event->id)
                ->where('form_id', $event->form_id)
                ->where('departament_id', $event->departament_id)
                ->first();

            $response['structure'] = $oldEvent->saved_structure;
            $response['results'] = FormResult::where('event_id', $oldEvent->id)->get();

            $oldFields = collect(json_decode($oldEvent->form_structure)->fields)
                ->keyBy('id')
                ->map(function ($field) {
                    $field->name = mb_strtolower($field->name);
                    return $field;
                });

            foreach (json_decode($event->form_structure)->fields as $field) {
                if (empty($response['results']->where('id', $field->id)->count())) {
                    if (empty($oldFields->get($field->id))) {
                        $finded = $oldFields->where('name', mb_strtolower($field->name))->first();

                        if (empty($finded) == false) {
                            $response['results']->where('field_id', $finded->id)
                                ->map(function (FormResult $formResult) use ($field) {
                                    $formResult->field_id = $field->id;
                                    return $formResult;
                                });
                        }
                    }
                }
            }

            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable | Exception $e) {
            return Responser::returnError([$e->getMessage()]);
        }
    }

    public function percent(Request $request): JsonResponse
    {
        try {
            $response = [];

            $user = $request->user();
            throw_if(empty($user));

            $event = Event::find($request->input('event_id', null));
            throw_if(empty($event), new HumanException('601, Ошибка проверки формы!'));

            if ($user->hasAnyAccess(['platform.supervisor.base', 'platform.min.base']) == false) {
                throw_if(empty($user->departament_id), new HumanException('600, Ошибка проверки пользователя!'));
                throw_if($event->departament_id != $user->departament_id, new HumanException('602, Ошибка проверки пользователя!'));
            }

            $response['percent'] = FormHelper::getPercent($event);

            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError([$e->getMessage()]);
        }
    }

    public function formFieldBlockeds(Request $request): JsonResponse
    {
        try {
            $response = [];

            $user = $request->user();
            throw_if(empty($user), new HumanException('101, Ошибка авторизации!'));

            $form = Form::find($request->input('form', null));
            throw_if(empty($form), new HumanException('102, Ошибка обработки формы!'));

            $formFieldBlockeds = FormFieldBlocked::where('form_id', $form->id)->get();

            $response = [
                'blockeds' => $formFieldBlockeds,
            ];

            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable) {
            return Responser::returnError(['100, Ошибка сервера!']);
        }
    }

    public function saveFieldBlockeds(Request $request): JsonResponse
    {
        try {
            if ($request->input('json', false)) {
                $fields = $request->input('fields', []);

                foreach ($fields as $key => $value) {
                    $fields[$key] = json_decode($value[0], true);
                }

                $request->merge(['fields' => $fields]);
            }

            $fields = Field::query()
                ->whereIn('id', array_keys($request->input('fields', [])))
                ->get();

            $form = Form::where('id', $fields->first()->form_id)->first();

            FormFieldBlocked::where('form_id', $form->id)->get()->map(fn(FormFieldBlocked $fieldBlocked) => $fieldBlocked->delete());

            foreach ($request->input('fields', []) as $fieldIdentifier => $fields) {
                foreach ($fields as $index => $value) {
                    if (empty(trim($value))) {
                        continue;
                    }

                    (new FormFieldBlocked())->fill([
                        'value' => $value,
                        'form_id' => $form->id,
                        'field_id' => $fieldIdentifier,
                        'index' => $index,
                    ])->save();
                }
            }

            return Responser::returnSuccess();
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable) {
            return Responser::returnError(['100, Ошибка сервера!']);
        }
    }

    public function archive(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $departament = Departament::find($user->departament_id);
            $event = Event::find($request->input('event'));
            $events = Event::query()
                ->where('form_id', $event->form_id)
                ->where('departament_id', $departament->id)
                ->whereNotNull('filled_at')
                ->get();
            return Responser::returnSuccess(['events' => $events]);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable) {
            return Responser::returnError(['100, Ошибка сервера!']);
        }
    }

    public function byInitiative(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $filledAt = Carbon::createFromTimestamp(strtotime($request->input('filled_at', now()->format("d.m.Y H:i:s"))));
            $departamentIdentifier = $user->departament_id;
            $departament = Departament::find($departamentIdentifier);
            $form = Form::find($request->input('identifier'));

            $event = new Event();

            $event->fill([
                'form_id' => $form->id,
                'form_structure' => $form->getStructure(),
                'departament_id' => $departament->id,
                'changing_filled_at' => $filledAt,
            ]);

            $event->save();

            return Responser::returnSuccess(['url' => "/forms?id={$event->id}"]);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable) {
            return Responser::returnError(['100, Ошибка сервера!']);
        }
    }
}
