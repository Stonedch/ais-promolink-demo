<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Form;
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
            return Responser::returnSuccess(FormHelper::byUser($user)->toArray());
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

            FormHelper::reinitResults($event, $request->input('fields', []), $user);

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

            $form = Form::find($request->input('form_id', null));
            throw_if(empty($form), new HumanException('Ошибка проверки формы!'));
            throw_if($form->is_editable != true, new HumanException('Ошибка проверки формы!'));

            $event = Event::orderBy('id', 'desc')->where('form_id', $form->id)->where('filled_at', '<>', null)->first();
            throw_if(empty($event), new HumanException('Ошибка проверки формы!'));
            throw_if($event->departament_id != $user->departament_id, new HumanException('Ошибка проверки пользователя!'));

            FormHelper::reinitResults($event, $request->input('fields', []), $user);

            $response = [];
            return Responser::returnSuccess($response);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError();
        }
    }
}
