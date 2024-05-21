<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Role;

use App\Orchid\Layouts\Role\RoleListLayout;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class RoleListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'roles' => Role::filters()->defaultSort('id', 'desc')->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Управление ролями';
    }

    public function description(): ?string
    {
        return 'Права доступа';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.roles',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить')
                ->icon('bs.plus-circle')
                ->href(route('platform.systems.roles.create')),
        ];
    }

    public function layout(): iterable
    {
        return [
            RoleListLayout::class,
        ];
    }
}
