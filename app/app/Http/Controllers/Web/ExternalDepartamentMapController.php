<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use App\Models\ExternalDepartament;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExternalDepartamentMapController extends Controller
{
    protected array $views = [
        'index' => 'web.external-departament-map.index',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        try {
            $user = $request->user();

            throw_if(empty($user), new HumanException('Ошибка авторизации! Номер ошибки: #1000'));
            throw_if($user->hasAnyAccess(['platform.min.base']) == false, new Exception('Ошибка авторизации! Номер ошибки: #1001'));

            $response = [
                'externalDepartaments' => Cache::remember(
                    'FormController.index.v0.[]',
                    now()->addDays(1),
                    fn () => ExternalDepartament::all(),
                ),
            ];

            return view($this->views['index'], $response);
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
}
