<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

ini_set('memory_limit', '-1');

class TestingController extends Controller
{
    public static array $views = [
        'index' => 'web.testing',
    ];

    public function index(): View|RedirectResponse
    {
        try {
            $this->checkAccess();
            return view(self::$views['index']);
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
        throw_if(empty($user), new HumanException('Ошибка авторизации!'));
    }
}
