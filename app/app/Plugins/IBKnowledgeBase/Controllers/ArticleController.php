<?php

namespace App\Plugins\IBKnowledgeBase\Controllers;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use App\Plugins\IBKnowledgeBase\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public const VIEWS = [
        'index' => 'IBKnowledgeBase::articles.index',
        'show' => 'IBKnowledgeBase::articles.show',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        try {
            $query = Article::with('pictures')->orderBy('id', 'desc');

            if ($request->has('tag')) {
                $tag = $request->input('tag');
                $query = $query->where('tags', 'ILIKE', "%{$tag}%");
            }

            if ($request->has('title')) {
                $name = $request->input('title');
                $query = $query->where('title', 'ILIKE', "%{$name}%");
            }

            $articles = $query->get();

            return view(self::VIEWS['index'], ['articles' => $articles]);
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
            return view(self::VIEWS['index'], ['article' => $article]);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        }
    }
}
