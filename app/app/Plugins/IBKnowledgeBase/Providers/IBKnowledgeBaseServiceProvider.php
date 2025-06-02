<?php

namespace App\Plugins\IBKnowledgeBase\Providers;

use App\Plugins\PluginServiceProvider;
use App\Plugins\PluginServiceSupport;

class IBKnowledgeBaseServiceProvider extends PluginServiceProvider
{
    protected $pluginName = 'IBKnowledgeBase';

    public function boot()
    {
        parent::boot();
    }

    protected function pluginBoot(): void {}

    public static function getPluginName(): string
    {
        return 'База знаний по ИБ / Шаблоны документов по ИБ';
    }

    public static function getPluginDescription(): string
    {
        return 'Данный модуль представляет собой древовидный каталог статей, предназначенный для специалистов по информационной безопасности организаций, зарегистрированных в системе';
    }

    public static function isActive(): bool
    {
        return in_array(self::class, PluginServiceSupport::getActiveServices()->toArray());
    }
}
