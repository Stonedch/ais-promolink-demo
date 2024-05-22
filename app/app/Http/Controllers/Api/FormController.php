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

            $response = Cache::remember("FormController.index.v0.{$user->id}", now()->addHour(), function () use ($user) {
                throw_if(empty($user));
                throw_if(empty($user->departament_id), new HumanException('Ошибка проверки пользователя!'));

                $departament = Departament::find($user->departament_id);

                throw_if(empty($departament), new HumanException('Ошибка проверки пользователя!'));
                throw_if(empty($departament->departament_type_id), new HumanException('Ошибка проверки ведомства!'));

                $forms = Form::query()
                    ->where('is_active', true)
                    ->whereIn('id', FormDepartamentType::where('departament_type_id', $departament->departament_type_id)->pluck('form_id'))
                    ->get();

                $fields = Field::whereIn('form_id', $forms->pluck('id'))->get();

                $collections = Collection::whereIn('id', $fields->pluck('collection_id'))->get();

                $collectionValues = CollectionValue::whereIn('collection_id', $collections->pluck('id'))->get();

                $events = Event::query()
                    ->where('departament_id', $departament->id)
                    ->where('filled_at', null)
                    ->get();

                return [
                    'forms' => $forms,
                    'fields' => $fields->groupBy('form_id'),
                    'collections' => $collections,
                    'collectionValues' => $collectionValues->groupBy('collection_id'),
                    'events' => $events,
                ];
            });

            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError();
        }
    }
}
