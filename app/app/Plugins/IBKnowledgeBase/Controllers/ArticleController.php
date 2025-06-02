<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use App\Plugins\IBKnowledgeBase\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ArticleController extends Controller
{
    public const VIEWS = [
        'index' => 'web.home.index',
        'show' => '',
    ];

    public function index(): View|RedirectResponse
    {
        try {
            return view(self::CONST['index'], ['articles' => Article::with('pictures')->get()]);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        }
    }

    public function show(int $article): View|RedirectResponse
    {
        try {
            $article = Article::with(['attachment', 'pictures'])->where('id', $article)->first();
            return view(self::CONST['index'], ['article' => $article]);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        }
    }
}
