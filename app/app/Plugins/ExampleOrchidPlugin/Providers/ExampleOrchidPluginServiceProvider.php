<?php

namespace App\Plugins\ExampleOrchidPlugin\Providers;

use App\Plugins\PluginServiceProvider;

class ExampleOrchidPluginServiceProvider extends PluginServiceProvider
{
    protected $pluginName = 'ExampleOrchidPlugin';

    public function boot()
    {
        parent::boot();
    }

    protected function pluginBoot(): void {}

    public static function getPluginName(): string
    {
        return 'Пример плагина с функционалом Orchid';
    }

    public static function getPluginDescription(): string
    {
        return 'Реализация плагина с функционалом Orchid в качестве примера работы';
    }
}
