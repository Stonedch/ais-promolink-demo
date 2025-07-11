<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Plugins\PluginServiceSupport;
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
        $menu = [
            Menu::make('Главная')
                ->icon('bs.house')
                ->route(config('platform.index')),

            Menu::make('Сайт')
                ->route('web.home.index')
                ->icon('bs.globe')
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

            Menu::make('Внешние учреждения')
                ->icon('bs.bank')
                ->route('platform.external-departaments')
                ->permission('platform.external-departaments.list')
                ->divider(),

            Menu::make('Типы загружаемых документов')
                ->icon('bs.pencil-square')
                ->route('platform.custom-report-types')
                ->permission('platform.custom-reports.base')
                ->canSee(config('app.custom_reports'))
                ->divider(config('app.custom_reports') == false),

            Menu::make('Лог загружаемых документов')
                ->icon('bs.database')
                ->route('platform.custom-report-logs')
                ->permission('platform.custom-reports.base')
                ->canSee(config('app.custom_reports')),

            Menu::make('Лог загружаемых документов по учреждениям')
                ->icon('bs.database')
                ->route('platform.custom-report-logs.by-departaments')
                ->permission('platform.custom-reports.base')
                ->canSee(config('app.custom_reports'))
                ->divider(),

            Menu::make('Бот-уведомления')
                ->icon('bs.chat-square-dots')
                ->route('platform.bot-notifications')
                ->permission('platform.bot_users.base'),

            Menu::make('Вопрос бот-пользователей')
                ->icon('bs.chat-square-dots')
                ->route('platform.bot-user-questions')
                ->permission('platform.bot_users.base')
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

            Menu::make('Подведомства')
                ->icon('bs.bank')
                ->route('platform.subdepartaments.departaments')
                ->permission('platform.subdepartaments.base')
                ->title('Управление подведомствами')
                ->divider(),
        ];

        PluginServiceSupport::getActiveServices()->map(function (string $plugin) use (&$menu) {
            $menu = array_merge($menu, $plugin::getMenu());
        });

        return $menu;
    }

    public function permissions(): array
    {
        $permissions = [
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
                ->addPermission('platform.forms.edit', 'Редактирование')
                ->addPermission('platform.forms.admin-edit', 'Доступ к административному исправлению'),

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

            ItemPermission::group('Права роли "Министр"')
                ->addPermission('platform.min.base', 'Основные'),

            ItemPermission::group('Права роли "Проверяющий"')
                ->addPermission('platform.checker.base', 'Основные'),

            ItemPermission::group('Внешние учреждения')
                ->addPermission('platform.external-departaments.list', 'Список')
                ->addPermission('platform.external-departaments.edit', 'Редактирование'),

            ItemPermission::group('Загружаемые документы')
                ->addPermission('platform.custom-reports.base', 'Модерация')
                ->addPermission('platform.custom-reports.loading', 'Загрузка'),

            ItemPermission::group('Бот-рассылка')
                ->addPermission('platform.bot_users.base', 'Основные'),

            ItemPermission::group('Подведомства')
                ->addPermission('platform.subdepartaments.base', 'Основные'),
        ];

        PluginServiceSupport::getActiveServices()->map(function (string $plugin) use (&$permissions) {
            $permissions = array_merge($permissions, $plugin::getPermissions());
        });

        return $permissions;
    }
}
