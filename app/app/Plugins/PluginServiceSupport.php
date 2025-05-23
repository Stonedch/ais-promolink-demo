<?php

namespace App\Plugins;

use Illuminate\Support\Collection;

class PluginServiceSupport
{
    public static function getActiveServices(): Collection
    {
        $actives = config('plugins.active', null);

        return empty($actives)
            ? new Collection()
            : collect(explode(';', $actives))
            ->map(
                fn(string $pluginName): string => "App\\Plugins\\{$pluginName}\\Providers\\{$pluginName}ServiceProvider"
            );
    }
}
