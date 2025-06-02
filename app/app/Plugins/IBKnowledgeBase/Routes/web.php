<?php

use Illuminate\Support\Facades\Route;

// [web.] /
Route::name('web.')->group(function () {
    // [web.ibkb.] /ibkb
    Route::name('ibkb.')->prefix('ibkb/')->group(function () {
        // [web.ibkb.articles]
        Route::name('articles.')->prefix('articles/')->group(function () {
            // Route::get('/', 'index')->name('index');
            // Route::get('/{article}', 'show')->name('show');
        });
    });
});
