<?php

namespace App\Plugins\EntityLogger\Providers;

use App\Plugins\PluginServiceProvider;
use App\Plugins\PluginServiceSupport;
use Orchid\Platform\ItemPermission;
use Orchid\Screen\Actions\Menu;

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

    public static function getMenu(): array
    {
        return [
            Menu::make('Учет истории сущностей')
                ->icon('bs.archive')
                ->permission('platform.plugins.entity-logger.base')
                ->route('platform.plugins.entity-logger.log.list'),
        ];
    }

    public static function getPermissions(): array
    {
        return [
            ItemPermission::group('Учет истории изменений сущностей')
                ->addPermission('platform.plugins.entity-logger.base', 'Базовые права'),
        ];
    }
}
