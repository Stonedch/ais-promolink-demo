<?php

use Illuminate\Support\Facades\Route;

// [web.] /
Route::name('web.')->group(function () {
    // [web.plugins] /
    Route::name('plugins.')->prefix('plugins/')->group(function () {
        // [web.plugins.ibkb.] /ibkb
        Route::name('ibkb.')->prefix('ibkb/')->group(function () {
            // [web.plugins.ibkb.articles]
            Route::name('articles.')->prefix('articles/')->controller(\App\Plugins\IBKnowledgeBase\Controllers\ArticleController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{article}', 'show')->name('show');
            });
        });
    });
});
