<?php

namespace App\Plugins\AccountingVulnerabilities\Providers;

use App\Plugins\PluginServiceProvider;
use Orchid\Platform\ItemPermission;
use Orchid\Screen\Actions\Menu;

class AccountingVulnerabilitiesServiceProvider extends PluginServiceProvider
{
    protected $pluginName = 'AccountingVulnerabilities';

    public function boot()
    {
        parent::boot();
    }

    protected function pluginBoot(): void {}

    public static function getPluginName(): string
    {
        return 'Сбор и учет уязвимостей';
    }

    public static function getPluginDescription(): string
    {
        return 'Сбор и учет уязвимостей';
    }

    public static function getMenu(): array
    {
        return [
            Menu::make('Сбор и учет уязвимостей')
                ->icon('bs.server')
                ->route('platform.plugins.vulnerabilities')
                ->permission('platform.plugins.accounting-vulnerabilities.base'),
        ];
    }

    public static function getPermissions(): array
    {
        return [
            ItemPermission::group('Сбор и учет уязвимостей')
                ->addPermission('platform.plugins.accounting-vulnerabilities.base', 'Базовые права'),
        ];
    }
}
