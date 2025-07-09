<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HumanException;
use App\Services\Api\Responser;
use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;

class EventStoreController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            throw_if(empty($user));
            throw_if($user->hasAccess('platform.events.create') == false, new HumanException("Ошибка авторизации"));

            if ($request->has('departamentType')) {
                $response['forms'] = Form::query()
                    ->select('forms.*')
                    ->join('form_departament_types', 'form_departament_types.form_id', '=', 'forms.id')
                    ->where('form_departament_types.departament_type_id', $request->input('departamentType'))
                    ->get()
                    ->pluck('id');
            } elseif ($request->has('district')) {
                $response['forms'] = Form::query()
                    ->select('forms.*')
                    ->join('form_departament_types', 'form_departament_types.form_id', '=', 'forms.id')
                    ->join('departaments', 'departaments.departament_type_id', '=', 'form_departament_types.id')
                    ->where('departaments.district_id', $request->input('district'))
                    ->get()
                    ->pluck('id');
            } else {
                $response['forms'] = Form::all()->pluck('id');
            }

            return Responser::returnSuccess(@$response ?: []);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError([$e->getMessage()]);
        }
    }
}
