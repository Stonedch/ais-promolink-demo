<?php

use Illuminate\Support\Facades\Route;

// web
Route::name('web.')->group(function () {
    // web.index
    Route::name('index.')->prefix('/')->controller(\App\Http\Controllers\Web\IndexController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });

    // web.home
    Route::name('home.')->prefix('/home')->controller(\App\Http\Controllers\Web\HomeController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });

    // web.forms
    Route::name('forms.')->prefix('/forms')->controller(\App\Http\Controllers\Web\FormController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/preview/{departament}/{form}', 'preview')->name('preview');
    });

    // web.auth
    Route::name('auth.')->prefix('/auth')->group(function () {
        // web.auth.login
        Route::name('login.')->prefix('/login')->controller(\App\Http\Controllers\Web\LoginController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/login', 'login')->name('login');
        });

        // web.auth.logout
        Route::name('logout.')->prefix('/logout')->controller(\App\Http\Controllers\Web\LogoutController::class)->group(function () {
            Route::get('/', 'index')->name('index');
        });
    });

    // web.minister
    Route::name('minister.')->prefix('/minister')->controller(\App\Http\Controllers\Web\MinisterController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/by-district/{district?}/{departament?}', 'byDistrict')->name('by-district');
        Route::get('/by-departament-type/{departamentType?}/{district?}/{departament?}', 'byDepartamentType')->name('by-departament-type');
    });

    // web.debug
    Route::resource('debug', \App\Http\Controllers\Web\DebugController::class);

    Route::name('external-departament-map.')->prefix('/external-departament-map')->controller(\App\Http\Controllers\Web\ExternalDepartamentMapController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });
});

// api
Route::name('api.')->prefix('/api')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    // api.auth
    Route::name('auth.')->prefix('/auth')->group(function () {
        //api.auth.login
        Route::name('login.')->prefix('/login')->controller(\App\Http\Controllers\Api\LoginController::class)->group(function () {
            Route::post('/login', 'login')->name('login');
        });
    });

    // api.forms
    Route::name('forms.')->prefix('/forms')->controller(\App\Http\Controllers\Api\FormController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/create', 'create')->name('create');
        Route::post('/edit', 'edit')->name('edit');
        Route::post('/save-draft', 'saveDraft')->name('save-draft');
        Route::post('/percent', 'percent')->name('percent');
    });

    // api.event-store
    Route::name('event-store.')->prefix('/event-store')->controller(\App\Http\Controllers\Api\EventStoreController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });

    // api.user-avatar
    Route::name('user-avatar')->prefix('/user-avatar')->controller(\App\Http\Controllers\Api\UserAvatarController::class)->group(function () {
        Route::post('/', 'store')->name('store');
    });

    // api.debug
    Route::apiResource('debug', \App\Http\Controllers\Api\DebugController::class);

    // api.notification
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])->name('notifications.read');
});
