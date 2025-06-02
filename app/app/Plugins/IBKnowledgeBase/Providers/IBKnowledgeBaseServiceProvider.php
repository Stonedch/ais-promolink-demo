<?php

namespace App\Plugins\IBKnowledgeBase\Providers;

use App\Plugins\PluginServiceProvider;
use App\Plugins\PluginServiceSupport;
use Orchid\Platform\ItemPermission;
use Orchid\Screen\Actions\Menu;

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


    public static function getMenu(): array
    {
        return [
            Menu::make('База знаний по ИБ')
                ->icon('bs.newspaper')
                ->permission('platform.plugins.ibkb.base')
                ->route('platform.plugins.ibkb.article.list'),
        ];
    }

    public static function getPermissions(): array
    {
        return [
            ItemPermission::group('База знаний по ИБ')
                ->addPermission('platform.plugins.ibkb.base', 'Базовые права'),
        ];
    }
}
