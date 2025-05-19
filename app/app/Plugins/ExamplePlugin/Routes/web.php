<?php

use Illuminate\Support\Facades\Route;

// [web.] /
Route::name('web.')->group(function () {
    // [web.example-plugin.] /example-plugin/
    Route::name('example-plugin.')->prefix('/example-plugin')->controller(\App\Plugins\ExamplePlugin\Controllers\ExamplePluginController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });
});
