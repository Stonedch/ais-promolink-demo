<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HumanException;
use App\Helpers\PhoneNormalizer;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LoginController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = [
                'phone' => PhoneNormalizer::normalizePhone($request->input('phone', '')),
                'password' => $request->input('password', null),
            ];

            throw_if(empty($credentials['phone']), new HumanException('Поле "Номер телефона" обязательно!'));
            throw_if(empty($credentials['password']), new HumanException('Поле "Пароль" обязательно!'));

            $remember = $request->input('remember', false);

            $successs = Auth::attempt($credentials, $remember);

            throw_if($successs == false, new HumanException('Неверный логин или пароль!'));

            return Responser::returnSuccess();
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError();
        }
    }
}
