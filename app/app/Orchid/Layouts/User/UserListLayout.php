<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\BotUser;
use App\Models\Departament;
use App\Orchid\Components\DateTimeRender;
use App\Orchid\Components\HumanizePhone;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Throwable;

class UserListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'users';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(User $user) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Edit'))
                            ->route('platform.systems.users.edit', $user->id)
                            ->icon('bs.pencil'),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $user->id,
                            ]),
                    ])),

            TD::make('id', '#')
                ->filter(Input::make())
                ->sort(),

            TD::make('phone', 'Номер телефона')
                ->usingComponent(HumanizePhone::class)
                ->sort()
                ->filter(Input::make()),

            TD::make('departament_id', 'Учреждение')
                ->sort()
                ->filter(TD::FILTER_SELECT, Departament::pluck('name', 'id'))
                ->render(function (User $user) {
                    try {
                        return Departament::find($user->departament_id)->name;
                    } catch (Throwable $e) {
                        return '-';
                    }
                }),

            TD::make('last_name', 'Фамилия')
                ->filter(Input::make())
                ->width(200),

            TD::make('first_name', 'Имя')
                ->filter(Input::make())
                ->width(200),

            TD::make('middle_name', 'Отчество')
                ->filter(Input::make())
                ->width(200),

            TD::make('', 'Установлен аватар?')
                ->width(200)
                ->render(function (User $user) {
                    return empty($user->attachment_id)
                        ? '<b class="badge bg-danger col-auto ms-auto">Нет</b>'
                        : '<b class="badge bg-success col-auto ms-auto">Да</b>';
                }),

            TD::make('', 'Телеграм')
                ->width(200)
                ->render(function (User $user) {
                    try {
                        return empty(BotUser::where('user_id', $user->id)->count())
                            ? '<b class="badge bg-danger col-auto ms-auto">Нет</b>'
                            : '<b class="badge bg-success col-auto ms-auto">Есть</b>';
                    } catch (Throwable) {
                        return '-';
                    }
                }),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeRender::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', __('Last edit'))
                ->usingComponent(DateTimeRender::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),
        ];
    }
}
