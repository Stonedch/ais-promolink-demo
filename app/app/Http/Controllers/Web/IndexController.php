<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Throwable;

class IndexController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        try {
            throw_if(
                empty($request->user()),
                new AuthorizationException('Для продолжения необходимо авторизоваться!')
            );

            $errors = session()->get('errors', app(ViewErrorBag::class));

            return redirect()
                ->route('web.home.index')
                ->withErrors(@$errors->getBag('default')->all());
        } catch (AuthorizationException $e) {
            return redirect()
                ->route('web.auth.login.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
