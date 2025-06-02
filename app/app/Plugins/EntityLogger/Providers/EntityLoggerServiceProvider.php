<?php

namespace App\Plugins\EntityLogger\Providers;

use App\Plugins\PluginServiceProvider;
use App\Plugins\PluginServiceSupport;

class EntityLoggerServiceProvider extends PluginServiceProvider
{
    protected $pluginName = 'EntityLogger';

    public function boot()
    {
        parent::boot();
    }

    protected function pluginBoot(): void {}

    public static function getPluginName(): string
    {
        return 'Учет истории изменений сущностей';
    }

    public static function getPluginDescription(): string
    {
        return 'Данный модуль представляет собой учет истории изменений сущностей';
    }

    public static function isActive(): bool
    {
        return in_array(self::class, PluginServiceSupport::getActiveServices()->toArray());
    }
}
