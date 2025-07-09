<?php

namespace App\Plugins\Esia\Controllers;

use App\Exceptions\HumanException;
use App\Services\Normalizers\PhoneNormalizer;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Plugins\Esia\Services\EsiaService;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Throwable;

class EsiaController extends Controller
{
    public function index(Request $request): ?RedirectResponse
    {
        if ($request->has('result')) return null;

        try {
            $token = $request->input('token');
            throw_if(empty($token), new HumanException("Ошибка получения данных,  #101"));
            $code = $request->input('code');
            throw_if(empty($code), new HumanException("Ошибка получения данных, #102"));

            $esia = new EsiaService($code, $token);

            $contacts = $esia->contacts();
            $person = $esia->person();

            $user = User::query()
                ->where('is_active', 'true')
                ->where(fn(Builder $query) => $query
                    ->where('oid', $esia->oid())
                    ->orWhere('phone', PhoneNormalizer::normalizePhone($contacts['MBT'])))
                ->first();

            throw_if(empty($user), new HumanException('Вы не зарегистрированы в системе'));

            $user->update([
                'email' => @$contacts['EML'],
                'last_name' => @$person['lastName'],
                'first_name' => @$person['firstName'],
                'middleName' => @$person['middleName'],
                'oid' => $esia->oid(),
                'password' => $user->password ?: md5($esia->oid()),
            ]);

            auth()->login($user);

            return redirect(route('web.home.index'));
        } catch (HumanException $e) {
            abort(400, $e->getMessage());
        } catch (Throwable $e) {
            abort(500, 'Ошибка сервера');
        }
    }
}
