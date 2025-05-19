<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PluginsLoaderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerPlugins();
    }

    protected function registerPlugins()
    {
        $activePlugins = explode(';', config('plugins.active', ''));

        foreach ($activePlugins as $pluginName) {
            $providerClass = "App\\Plugins\\{$pluginName}\\Providers\\{$pluginName}ServiceProvider";

            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }
}
