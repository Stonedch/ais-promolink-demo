<?php

namespace App\Plugins\Esia\Controllers;

use App\Exceptions\HumanException;
use App\Services\Api\Responser;
use App\Http\Controllers\Controller;
use App\Plugins\Esia\Services\CryptoPro;
use App\Plugins\Esia\Services\EsiaService;
use App\Plugins\Esia\Services\GuidTokenizer;
use ErrorException;
use Illuminate\Http\JsonResponse;
use Throwable;

class ApiEsiaController extends Controller
{
    public function url(): JsonResponse
    {
        try {
            $body = [
                'state' => GuidTokenizer::tokenize(),
                'client_id' => config('plugins.Esia.clientid'),
                'scope' => config('plugins.Esia.scope'),
                'timestamp' => date("Y.m.d H:i:s +0000"),
                'redirect_uri' => EsiaService::redirect(),
            ];

            $body['client_secret'] = CryptoPro::getClientSecret("{$body['scope']}{$body['timestamp']}{$body['client_id']}{$body['state']}");
            $url = 'https://esia.gosuslugi.ru/aas/oauth2/ac?access_type=online&response_type=code&' . http_build_query($body);

            return Responser::returnSuccess(['url' => $url]);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage(), $e->getCode()]);
        } catch (ErrorException $e) {
            return Responser::returnError(['Ошибка сервера', 101, $e->getMessage()]);
        } catch (Throwable) {
            return Responser::returnError(['Ошибка сервера', 100]);
        }
    }
}
