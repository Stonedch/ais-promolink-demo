<?php

namespace App\Http\Controllers\Web;

use App\Services\Normalizers\PhoneNormalizer;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(): View
    {
        return view('web.auth.login.index');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'phone' => ['required'],
            'password' => ['required'],
        ], [
            'phone.required' => 'Поле "Номер телефона" обязательно!',
            'password.required' => 'Поле "Пароль" обязательно!',
        ]);

        $remember = $request->input('remember', false);
        $credentials['phone'] = PhoneNormalizer::normalizePhone($credentials['phone']);

        if (Auth::attempt($credentials, $remember)) {
            return redirect()->intended(route('web.index.index'));
        } else {
            return redirect()
                ->route('web.auth.login.index')
                ->withErrors(['Логин или пароль введены неверно!']);
        }

    }
}
