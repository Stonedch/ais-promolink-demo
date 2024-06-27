<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FormController extends Controller
{
    protected array $views = [
        'single' => 'web.form.single',
        'show' => 'web.form.show',
        'preview' => 'web.form.preview',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        try {
            $user = $request->user();
            throw_if(empty($user), new HumanException('Ошибка авторизации!'));
            return view($this->views['single']);
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

    public function show(Request $request): View|RedirectResponse
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

    public function preview(Request $request, Form $form): View|Redirect
    {
        try {
            $user = $request->user();
            throw_if(empty($user), new HumanException('Ошибка авторизации!'));
            $response = ['form' => $form];
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
