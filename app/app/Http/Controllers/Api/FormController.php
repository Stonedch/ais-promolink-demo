<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HumanException;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionValue;
use App\Models\Departament;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormDepartamentType;
use App\Models\FormResult;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

class FormController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            throw_if(empty($user));
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

            $response = [
                'forms' => $forms,
                'fields' => $fields->groupBy('form_id'),
                'collections' => $collections,
                'collectionValues' => $collectionValues->groupBy('collection_id'),
                'events' => $events,
            ];

            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError();
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $response = [];

            $user = $request->user();
            throw_if(empty($user));
            throw_if(empty($user->departament_id), new HumanException('Ошибка проверки пользователя!'));

            $event = Event::find($request->input('event_id', null));
            throw_if(empty($event), new HumanException('Ошибка проверки формы!'));
            throw_if($event->departament_id != $user->departament_id, new HumanException('Ошибка проверки пользователя!'));
            throw_if(empty($event->filled_at) == false, new HumanException('Ошибка проверки формы!'));

            FormResult::query()
                ->where('event_id', $event->id)
                ->delete();

            $structure = json_decode($event->form_structure);

            foreach ($structure->fields as $field) {
                $formResult = new FormResult();

                $formResult->fill([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'field_id' => $field->id,
                    'value' => $request->input("fields.$field->id", null),
                ]);

                $formResult->save();
            }

            $event->filled_at = now();
            $event->save();

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

            $event = Event::find($request->input('event_id', null));
            throw_if(empty($event), new HumanException('Ошибка проверки формы!'));
            throw_if($event->departament_id != $user->departament_id, new HumanException('Ошибка проверки пользователя!'));

            $form = Form::find($event->form_id);
            throw_if(empty($form), new HumanException('Ошибка проверки формы!'));
            throw_if($form->is_editable != true, new HumanException('Ошибка проверки формы!'));

            FormResult::query()
                ->where('event_id', $event->id)
                ->delete();

            $structure = json_decode($event->form_structure);

            foreach ($structure->fields as $field) {
                $formResult = new FormResult();

                $formResult->fill([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'field_id' => $field->id,
                    'value' => $request->input("fields.$field->id", null),
                ]);

                $formResult->save();
            }

            $event->filled_at = $event->filled_at ?: now();
            $event->refilled_at = now();
            $event->save();

            $response = [];
            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError();
        }
    }
}
