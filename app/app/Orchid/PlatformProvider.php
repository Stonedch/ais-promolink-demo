<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    public function menu(): array
    {
        return [
            Menu::make('Главная')
                ->icon('bs.house')
                ->route(config('platform.index'))
                ->divider(),

            Menu::make('Коллекции')
                ->icon('bs.book')
                ->route('platform.collections')
                ->permission('platform.collections.list')
                ->divider(),

            Menu::make('Типы ведомств')
                ->icon('bs.card-text')
                ->route('platform.departament-types')
                ->permission('platform.departament-types.list')
                ->title('Ведомства'),

            Menu::make('Ведомства')
                ->icon('bs.bank')
                ->route('platform.departaments')
                ->permission('platform.departaments.list')
                ->divider(),

            Menu::make('Пользователи')
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title('Права доступа'),

            Menu::make('Роли')
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
        ];
    }

    public function permissions(): array
    {
        return [
            ItemPermission::group('Система')
                ->addPermission('platform.systems.roles', 'Роли')
                ->addPermission('platform.systems.users', 'Пользователи'),

            ItemPermission::group('Коллекции')
                ->addPermission('platform.collections.list', 'Список')
                ->addPermission('platform.collections.edit', 'Редактирование'),

            ItemPermission::group('Типы ведомств')
                ->addPermission('platform.departament-types.list', 'Список')
                ->addPermission('platform.departament-types.edit', 'Редактирование'),

            ItemPermission::group('Ведомства')
                ->addPermission('platform.departaments.list', 'Список')
                ->addPermission('platform.departaments.edit', 'Редактирование'),
        ];
    }
}
