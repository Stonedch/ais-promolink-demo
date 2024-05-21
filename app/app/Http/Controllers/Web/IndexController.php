<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (empty($user)) {
            return redirect()
                ->route('web.auth.login.index')
                ->withErrors(['Для продолжения необходимо авторизоваться!']);
        } else {
            return redirect()
                ->route('web.home.index');
        }
    }
}
