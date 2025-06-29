<?php

use App\Plugins\EntityLogger\Orchid\Screens\EntityLogListScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::screen('entity-logger/log/list', EntityLogListScreen::class)
    ->name('platform.plugins.entity-logger.log.list')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Учет истории изменений сущностей', route('platform.plugins.entity-logger.log.list')));
