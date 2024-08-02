<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormChecker;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

class FormCheckerController extends Controller
{
    public static array $views = [
        'index' => 'web.form-checker.index',
    ];

    public function index(): View|RedirectResponse
    {
        try {
            $user = Auth::user();

            $this->checkAccess($user);

            $formCheckers = FormChecker::query()
                ->where('user_id', $user->id)
                ->get();
            $events = Event::query()
                ->whereIn('form_id', $formCheckers->pluck('form_id'))
                ->get()
                ->map(function (Event $event) {
                    $event->form_structure = json_decode($event->form_structure);
                    return $event;
                })
                ->filter(function (Event $event) use ($user) {
                    return empty(collect($event->form_structure->fields)->where('checker_user_id', $user->id)->count()) == false;
                });
            $forms = Form::query()
                ->whereIn('id', $events->pluck('form_id'))
                ->get();
            $departaments = Departament::where('id', $events->pluck('departament_id'));

            return view(self::$views['index'], [
                'formCheckers' => $formCheckers,
                'forms' => $forms,
                'events' => $events,
                'departaments' => $departaments,
            ]);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            abort(500);
        }
    }

    protected function checkAccess(User $user = null): void
    {
        if (empty($user)) $user = Auth::user();
        throw_if(empty($user), new HumanException('Ошибка авторизации! Номер ошибки: #1100'));
        throw_if($user->hasAnyAccess(['platform.checker.base']) == false, new HumanException('Ошибка авторизации! Номер ошибки: #1101'));
    }
}
