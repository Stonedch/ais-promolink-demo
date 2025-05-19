<?php

use App\Plugins\ExampleOrchidPlugin\Orchid\Screens\ExampleOrchidPluginScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::screen('example-orchid-plugin', ExampleOrchidPluginScreen::class)
    ->name('platform.example-orchid-plugin')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Orchid Plugin', route('platform.example-orchid-plugin')));
