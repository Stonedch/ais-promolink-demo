<?php

use App\Plugins\AccountingVulnerabilities\Models\Vulnerability;
use App\Plugins\AccountingVulnerabilities\Orchid\Screens\VulnerabilityEditScreen;
use App\Plugins\AccountingVulnerabilities\Orchid\Screens\VulnerabilityHistoryListScreen;
use App\Plugins\AccountingVulnerabilities\Orchid\Screens\VulnerabilityListScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::screen('plugins/vulnerabilities', VulnerabilityListScreen::class)
    ->name('platform.plugins.vulnerabilities')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Сбор и учет уязвимостей', route('platform.plugins.vulnerabilities')));

Route::screen('plugins/vulnerabilities/create', VulnerabilityEditScreen::class)
    ->name('platform.plugins.vulnerabilities.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.plugins.vulnerabilities')
        ->push('Управление уязвимостью', route('platform.plugins.vulnerabilities.create')));

Route::screen('plugins/vulnerabilities/{vulnerability}/edit', VulnerabilityEditScreen::class)
    ->name('platform.plugins.vulnerabilities.edit')
    ->breadcrumbs(fn(Trail $trail, Vulnerability $vulnerability) => $trail
        ->parent('platform.plugins.vulnerabilities')
        ->push('Управление уязвимостью', route('platform.plugins.vulnerabilities.edit', $vulnerability)));

Route::screen('plugins/vulnerabilities/{vulnerability}/history', VulnerabilityHistoryListScreen::class)
    ->name('platform.plugins.vulnerabilities.history')
    ->breadcrumbs(fn(Trail $trail, Vulnerability $vulnerability) => $trail
        ->parent('platform.plugins.vulnerabilities')
        ->push('История уязвимости', route('platform.plugins.vulnerabilities.history', $vulnerability)));
