<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        try {
            $user = $request->user();
            throw_if(empty($user), new HumanException('Ошибка авторизации!'));
            return view('web.form.single');
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
