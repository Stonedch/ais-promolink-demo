<?php

use App\Plugins\IBKnowledgeBase\Models\Article;
use App\Plugins\IBKnowledgeBase\Orchid\Screens\ArticleEditScreen;
use App\Plugins\IBKnowledgeBase\Orchid\Screens\ArticleListScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::screen('ibkb/article/list', ArticleListScreen::class)
    ->name('platform.plugins.ibkb.article.list')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('База знаний по ИБ', route('platform.plugins.ibkb.article.list')));

Route::screen('ibkb/article/edit/{article}', ArticleEditScreen::class)
    ->name('platform.plugins.ibkb.article.edit')
    ->breadcrumbs(fn(Trail $trail, Article $article) => $trail
        ->parent('platform.plugins.ibkb.article.list')
        ->push('Редактирование статьи', route('platform.plugins.ibkb.article.edit', $article)));

Route::screen('ibkb/article/create', ArticleEditScreen::class)
    ->name('platform.plugins.ibkb.article.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.plugins.ibkb.article.list')
        ->push('Создание статьи', route('platform.plugins.ibkb.article.create')));