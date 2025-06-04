<?php

use Illuminate\Support\Facades\Route;

// [api.] /api
Route::name('api.')->prefix('/api')->group(function () {
    // [api.plugins.] /api/plugins
    Route::name('plugins.')->prefix('/plugins')->group(function () {
        // [api.plugins.esia] /api/plugins/esia
        Route::name('esia.')->prefix('/esia')->controller(\App\Plugins\Esia\Controllers\ApiEsiaController::class)->group(function () {
            Route::get('/url', 'url')->name('url');
        });
    });
});

// [web.] /
Route::name('web.')->prefix('/')->group(function () {
    // [web.plugins] /plugins
    Route::name('plugins.')->prefix('/plugins')->group(function () {
        //[web.plugins.esia] /plugins/esia
        Route::name('esia.')->prefix('/esia')->controller(\App\Plugins\Esia\Controllers\EsiaController::class)->group(function () {
            Route::get('/', 'index')->name('index');
        });
    });
});
