<?php

namespace App\Plugins\IBKnowledgeBase\Orchid\Screens;

use App\Models\User;
use App\Plugins\IBKnowledgeBase\Models\Article;
use Illuminate\Support\Facades\Cache;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class ArticleListScreen extends Screen
{
    public function name(): string
    {
        return 'База знаний по ИБ';
    }

    public function query(): iterable
    {
        return [
            'articles' => Article::filters()
                ->defaultSort('id', 'desc')
                ->with(['author', 'parent'])
                ->paginate(),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Добавить статью')
                ->icon('plus')
                ->route('platform.ibkb.article.create')

        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('articles', [
                TD::make('title', 'Название')
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn(Article $article) => Link::make($article->title)
                        ->route('platform.ibkb.article.edit', $article)),

                TD::make('author', 'Автор')
                    ->sort()
                    ->filter(
                        TD::FILTER_SELECT,
                        Cache::remember(
                            self::class . '::layout[authors]',
                            now()->addDay(),
                            fn(): iterable => User::get()->keyBy('id')->map(fn(User $user): string => $user->getFullname())
                        )
                    )
                    ->render(fn(Article $article): string => $article->author->getFullname()),

                TD::make('tags', 'Теги')
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn(Article $article) => str_replace(';', ' ', $article->tags)),

                TD::make('parent_id', 'Родитель')
                    ->sort()
                    ->filter(
                        TD::FILTER_SELECT,
                        Cache::remember(
                            self::class . '::layout[parents]',
                            now()->addDay(),
                            fn(): iterable => Article::get()->keyBy('id')->map(fn(Article $article): string => $article->title)
                        )
                    )
                    ->render(fn(Article $article): ?string => $article->parent ? $article->parent->title : null),

                TD::make('created_at', 'Дата создания')
                    ->sort()
                    ->render(fn(Article $article) => $article->created_at->toDateTimeString()),
            ]),
        ];
    }
}
