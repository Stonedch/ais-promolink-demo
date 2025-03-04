<?php

use App\Http\Middleware\ServiceUnavailable;
use App\Http\Middleware\LogRoute;
use Illuminate\Support\Facades\Route;

// web
Route::middleware([ServiceUnavailable::class, LogRoute::class])->name('web.')->group(function () {
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
        Route::get('/preview-structure/{form}', 'previewStructure')->name('preview-structure');
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
        Route::get('/reports', 'reports')->name('reports');
        Route::get('/by-district/{district?}/{departament?}', 'byDistrict')->name('by-district');
        Route::get('/by-departament-type/{departamentType?}/{district?}/{departament?}', 'byDepartamentType')->name('by-departament-type');
        Route::get('/by-form/{form?}', 'byForm')->name('by-form');
    });

    // web.debug
    Route::resource('debug', \App\Http\Controllers\Web\DebugController::class);

    // web.external-departament-map
    Route::name('external-departament-map.')->prefix('/external-departament-map')->controller(\App\Http\Controllers\Web\ExternalDepartamentMapController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });

    // web.form-checker
    Route::name('form-checker.')->prefix('/form-checker')->controller(\App\Http\Controllers\Web\FormCheckerController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/accept', 'accept')->name('accept');
        Route::get('/reject', 'reject')->name('reject');
    });

    // web.testing
    Route::name('testing.')->prefix('/testing')->controller(\App\Http\Controllers\Web\TestingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });

    // web.custom-reports
    Route::name('custom-reports.')->prefix('/custom-reports')->controller(\App\Http\Controllers\Web\CustomReportController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::withoutMiddleware([LogRoute::class])->get('/download-template', 'downloadTemplate')->name('download-template');
    });
});

// api
Route::middleware([ServiceUnavailable::class, LogRoute::class])->name('api.')->prefix('/api')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
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
        Route::post('/save-field-blockeds', 'saveFieldBlockeds')->name('save-field-blockeds');
        Route::post('/percent', 'percent')->name('percent');
        Route::post('/old-values', 'getOldValues')->name('get-old-values');
        Route::get('/form-field-blockeds', 'formFieldBlockeds')->name('form-field-blockeds');
        Route::get('/archive', 'archive')->name('archive');
        Route::post('/by-initiative', 'byInitiative')->name('by-initiative');
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

    // api.custom-report
    Route::name('custom-reports.')->prefix('/custom-reports')->controller(\App\Http\Controllers\Api\CustomReportController::class)->group(function () {
        Route::post('/store', 'store')->name('store');
    });

    // api.telegram
    Route::name('telegram.')->prefix('/telegram')->controller(\App\Http\Controllers\Api\TelegramController::class)->group(function () {
        Route::match(['GET', 'POST'], '/handle', 'handle')->name('handle');
    });
});

// owns
if (is_file(base_path('routes/owns.php'))) {
    Route::name('owns.')->group(base_path('routes/owns.php'));
}
