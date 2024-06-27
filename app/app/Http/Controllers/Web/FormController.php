<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormDepartamentType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FormController extends Controller
{
    protected array $views = [
        'show' => 'web.form.show',
        'preview' => 'web.form.preview',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        try {
            $user = $request->user();
            throw_if(empty($user), new HumanException('Ошибка авторизации!'));
            return view($this->views['show']);
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

    public function preview(Request $request, Departament $departament, Form $form): View|RedirectResponse
    {
        try {
            $user = $request->user();

            throw_if(empty($user), new HumanException('Ошибка авторизации! Номер ошибки: #1003.'));

            if ($user->hasAnyAccess(['platform.supervisor.base']) == false) {
                throw_if($user->departament_id != $departament->id, new HumanException('Ошибка авторизации! Номер ошибки: #1004.'));
            }

            throw_if($form->is_active == false, new HumanException('Ошибка обработки формы! Номер ошибки: #1000.'));

            $event = Event::query()
                ->where('form_id', $form->id)
                ->where('departament_id', $departament->id);

            if ($request->has('event')) {
                $event = $event->where('id', $request->input('event'))->first();
            } else {
                $event = $event->whereNotNull('filled_at')->orderBy('id', 'desc')->first();
            }

            $response = [
                'form' => $form,
                'departament' => $departament,
                'event' => $event,
            ];

            return view($this->views['preview'], $response);
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
