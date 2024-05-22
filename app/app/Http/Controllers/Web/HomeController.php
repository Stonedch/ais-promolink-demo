<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function index(): View|RedirectResponse
    {
        try {
            throw_if(empty(auth()->user()), new HumanException('Ошибка авторизации!'));
            return view('web.home.index');
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        }
    }
}
