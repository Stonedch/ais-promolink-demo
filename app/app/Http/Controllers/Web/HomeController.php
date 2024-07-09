<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use App\Models\DistrictDashboardParam;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class HomeController extends Controller
{
    public static array $views = [
        'index' => 'web.home.index',
        'min' => 'web.home.min',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        try {
            $user = Auth::user();
            throw_if(empty($user), new HumanException('Ошибка авторизации!'));

            if ($user->hasAnyAccess(['platform.supervisor.base', 'platform.min.base'])) {
                $response = FormHelper::byDepartaments(Departament::whereNotNull('departament_type_id')->get());
            } else {
                $response = FormHelper::byUser($user);
            }

            $view = self::$views['index'];

            if ($user->hasAnyAccess(['platform.min.base'])) {
                // TODO: костыль, убрать
                if ($request->has('district')) {
                    $response['dashboard'] = DistrictDashboardParam::query()
                        ->where('district_id', $request->input('district'))
                        ->orderBy('sort')
                        ->get();
                }

                $view = self::$views['min'];
            }

            return view($view, $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable) {
            abort(500);
        }
    }
}
