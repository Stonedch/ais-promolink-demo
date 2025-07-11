<?php

namespace App\Plugins\ExamplePlugin\Providers;

use App\Plugins\PluginServiceProvider;

class ExamplePluginServiceProvider extends PluginServiceProvider
{
    protected $pluginName = 'ExamplePlugin';

    public function boot()
    {
        parent::boot();
    }

    protected function pluginBoot(): void {}

    public static function getPluginName(): string
    {
        return 'Пример базового плагина';
    }

    public static function getPluginDescription(): string
    {
        return 'Реализация базового плагина в качестве примера работы';
    }
}
