<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

// [api.] /api
Route::name('api.')->prefix('/api')->withoutMiddleware([VerifyCsrfToken::class])->group(function () {
    // [api.plugins.] /api/plugins
    Route::name('plugins.')->prefix('/plugins')->group(function () {
        // [api.plugins.vulnerabilities] /api/plugins/vulnerabilities
        Route::name('vulnerabilities.')->prefix('/vulnerabilities')->controller(\App\Plugins\AccountingVulnerabilities\Controllers\Api\VulnerabilityController::class)->group(function () {
            Route::post('/create', 'create')->name('create');
        });
    });
});
