<?php

namespace App\Plugins;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Orchid\Platform\Providers\RouteServiceProvider;
use Orchid\Support\Facades\Dashboard;

class PluginServiceSupport
{
    public static function getActiveServices(): Collection
    {
        return collect(explode(';', config('plugins.active', '')))
            ->map(
                fn(string $pluginName): string => "App\\Plugins\\{$pluginName}\\Providers\\{$pluginName}ServiceProvider"
            );
    }
}
