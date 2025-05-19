<?php

namespace App\Plugins\ExamplePlugin\Controllers;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use App\Plugins\ExamplePlugin\Models\Example;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ExamplePluginController extends Controller
{
    protected const VIEWS = [
        'index' => 'ExamplePlugin::index',
    ];

    public function index(): View|RedirectResponse
    {
        try {
            if (empty(Example::count())) $this->makeExampleSeeds();
            return view(self::VIEWS['index'], ['examples' => Example::get()]);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable) {
            return redirect()
                ->route('web.index.index')
                ->withErrors(['Внутренняя ошибка']);
        }
    }

    protected function makeExampleSeeds(int $count = 10): void
    {
        for ($i = 0; $i < $count; $i++) {
            Example::create();
        }
    }
}
