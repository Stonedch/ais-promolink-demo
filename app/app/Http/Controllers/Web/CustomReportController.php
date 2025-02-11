<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomReportController extends Controller
{
    protected array $views = [
        'index' => 'web.custom-reports.index',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        try {
            $user = $request->user();

            throw_if(
                empty($user),
                new HumanException('Ошибка авторизации! Номер ошибки: #1000')
            );

            throw_if(
                $user->hasAnyAccess(['platform.custom-reports.loading']) == false,
                new HumanException('Ошибка доступа к функционалу кастомного отчета!')
            );

            return view($this->views['index']);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors(['msg' => $e->getMessage()]);
        } catch (Throwable $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors(['Внутренняя ошибка']);
        }
    }
}
