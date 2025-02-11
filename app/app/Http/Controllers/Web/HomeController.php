<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\District;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormDepartamentType;
use App\Models\FormResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

class HomeController extends Controller
{
    public static array $views = [
        'index' => 'web.home.index',
    ];

    public function index(): View|RedirectResponse
    {
        try {
            $user = Auth::user();

            $this->checkAccess($user);

            if ($user->hasAnyAccess(['platform.min.base'])) {
                return $this->indexMinister();
            } elseif ($user->hasAnyAccess(['platform.supervisor.base'])) {
                return $this->indexSupervisor();
            } else {
                if (empty($user->departament_id)) {
                    abort(403, 'У Вас отсутствует установленное ведомство!');
                }

                return $this->indexUser();
            }
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        }
    }

    private function indexMinister(): RedirectResponse
    {
        return redirect()->route('web.minister.index');
    }

    private function indexSupervisor(): View
    {
        $response = FormHelper::byDepartaments(
            Departament::whereNotNull('departament_type_id')->get(),
            arrayReturn: true
        );

        return view(self::$views['index'], $response);
    }

    private function indexUser(User $user = null): View
    {
        if (empty($user)) {
            $user = Auth::user();
        }

        $departaments = Departament::filters()->defaultSort('id', 'desc')->get();
        $departament = $departaments->where('id', $user->departament_id);

        throw_if(
            empty($departament),
            new HumanException('Ошибка проверки пользователя!')
        );

        $forms = Form::query()
            ->where('is_active', true)
            ->whereIn('id', Event::where('departament_id', $user->departament_id)->select('form_id'))
            ->get();

        $categories = FormCategory::query()
            ->whereIn('id', $forms->pluck('form_category_id'))
            ->get()
            ->keyBy('id');

        return view(self::$views['index'], [
            'user' => $user,
            'departament' => $departament,
            'departaments' => $departaments,
            'forms' => $forms,
            'formCategories' => $categories,
        ]);
    }

    private function checkAccess(User $user = null): void
    {
        if (empty($user))
            $user = Auth::user();
        throw_if(empty($user), new HumanException('Ошибка авторизации!'));
    }
}
