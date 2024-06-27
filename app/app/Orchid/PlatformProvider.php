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

            Menu::make('Формы')
                ->icon('bs.pencil-square')
                ->route('platform.forms')
                ->permission('platform.forms.list'),

            Menu::make('Категории форм')
                ->icon('bs.pencil-square')
                ->route('platform.form-categories')
                ->permission('platform.form-categories.list'),

            Menu::make('Коллекции')
                ->icon('bs.book')
                ->route('platform.collections')
                ->permission('platform.collections.list'),

            Menu::make('События')
                ->icon('bs.calendar-event')
                ->route('platform.events')
                ->permission('platform.events.list')
                ->divider(),

            Menu::make('Типы учреждений')
                ->icon('bs.card-text')
                ->route('platform.departament-types')
                ->permission('platform.departament-types.list')
                ->title('Учреждения'),

            Menu::make('Учреждения')
                ->icon('bs.bank')
                ->route('platform.departaments')
                ->permission('platform.departaments.list'),

            Menu::make('Районы')
                ->icon('bs.buildings')
                ->route('platform.districts')
                ->permission('platform.districts.list')
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

            ItemPermission::group('Типы учреждений')
                ->addPermission('platform.departament-types.list', 'Список')
                ->addPermission('platform.departament-types.edit', 'Редактирование'),

            ItemPermission::group('Учреждения')
                ->addPermission('platform.departaments.list', 'Список')
                ->addPermission('platform.departaments.edit', 'Редактирование'),

            ItemPermission::group('Формы')
                ->addPermission('platform.forms.list', 'Список')
                ->addPermission('platform.forms.edit', 'Редактирование'),

            ItemPermission::group('Категории форм')
                ->addPermission('platform.form-categories.list', 'Список')
                ->addPermission('platform.form-categories.edit', 'Редактирование'),

            ItemPermission::group('События')
                ->addPermission('platform.events.list', 'Список')
                ->addPermission('platform.events.create', 'Создание')
                ->addPermission('platform.events.edit', 'Редактирование'),

            ItemPermission::group('Результаты заполнения')
                ->addPermission('platform.form_results.list', 'Список'),

            ItemPermission::group('Районы')
                ->addPermission('platform.districts.list', 'Список')
                ->addPermission('platform.districts.edit', 'Редактирование'),

            ItemPermission::group('Права роли "Директор Учреждения"')
                ->addPermission('platform.departament-director.base', 'Основные'),

            ItemPermission::group('Права роли "Руководитель"')
                ->addPermission('platform.supervisor.base', 'Основные'),
        ];
    }
}
