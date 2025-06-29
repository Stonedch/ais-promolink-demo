<?php

namespace App\Plugins\Esia\Providers;

use App\Plugins\PluginServiceProvider;

class EsiaServiceProvider extends PluginServiceProvider
{
    protected $pluginName = 'Esia';

    public function boot()
    {
        parent::boot();
    }

    protected function pluginBoot(): void {}

    public static function getPluginName(): string
    {
        return 'ЕСИА';
    }

    public static function getPluginDescription(): string
    {
        return 'Модуль авторизации ЕСИА (Гос Услуги)';
    }
}
