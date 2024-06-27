<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(): View|RedirectResponse
    {
        try {
            $user = Auth::user();
            throw_if(empty($user), new HumanException('Ошибка авторизации!'));

            if ($user->hasAnyAccess(['platform.supervisor.base'])) {
                $response = FormHelper::byDepartaments(Departament::whereNotNull('departament_type_id')->get());
            } else {
                $response = FormHelper::byUser($user);
            }

            return view('web.home.index', $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        }
    }
}
