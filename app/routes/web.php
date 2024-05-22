<?php

use Illuminate\Support\Facades\Route;

Route::name('web.')->group(function () {
    Route::name('index.')->prefix('/')->controller(\App\Http\Controllers\Web\IndexController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::name('home.')->prefix('/home')->controller(\App\Http\Controllers\Web\HomeController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::name('auth.')->prefix('/auth')->group(function () {
        Route::name('login.')->prefix('/login')->controller(\App\Http\Controllers\Web\LoginController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/login', 'login')->name('login');
        });

        Route::name('logout.')->prefix('/logout')->controller(\App\Http\Controllers\Web\LogoutController::class)->group(function () {
            Route::get('/', 'index')->name('index');
        });
    });
});

Route::name('api.')->prefix('/api')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::name('auth.')->prefix('/auth')->group(function () {
        Route::name('login.')->prefix('/login')->controller(\App\Http\Controllers\Api\LoginController::class)->group(function () {
            Route::post('/login', 'login')->name('login');
        });
    });

    Route::name('forms.')->prefix('/forms')->controller(\App\Http\Controllers\Api\FormController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/create', 'create')->name('create');
        Route::get('/edit', 'edit')->name('edit');
    });
});
