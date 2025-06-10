<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Subdepartament;

use App\Models\Collection;
use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\District;
use App\Models\User;
use App\Orchid\Components\DateTimeRender;
use App\Orchid\Components\HumanizePhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

class DepartamentListScreen extends Screen
{
    public $departamentTypes;
    public $districts;
    public $departamentOptions;

    public function query(Request $request): iterable
    {
        $parentId = $request->input('filter.parent_id.0', $request->user()->departament_id);

        $users = User::where('departament_id', $parentId)->get();

        $departaments = Departament::query()
            ->where('parent_id', $parentId)
            ->with(['parent'])
            ->filters()
            ->defaultSort('id', 'desc')
            ->paginate(50);

        $this->departamentOptions = Departament::pluck('name', 'id');

        return [
            'departaments' => $departaments,
            'departamentOptions' => $this->departamentOptions,
            'departamentTypes' => $departaments->isNotEmpty()
                ? DepartamentType::whereIn('id', $departaments->pluck('departament_type_id') ?: [])->get()
                : new Collection(),
            'districts' => $departaments->isNotEmpty()
                ? District::whereIn('id', $departaments->pluck('district_id'))->get()
                : new Collection(),
            'users' => $users,
        ];
    }

    public function name(): ?string
    {
        return 'Подведомства';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.subdepartaments.base',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить подведомство')
                ->icon('bs.plus')
                ->href(route('platform.subdepartaments.departaments.create', ['parent_id' => request()->input('filter.parent_id.0', null)]))
                ->canSee(Auth::user()->hasAccess('platform.subdepartaments.base')),
            Link::make('Добавить пользователя')
                ->icon('bs.plus')
                ->href(route('platform.subdepartaments.users.create', ['parent_id' => request()->input('filter.parent_id.0', null)]))
                ->canSee(Auth::user()->hasAccess('platform.subdepartaments.base')),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('departaments', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->canSee(Auth::user()->hasAccess('platform.departaments.edit'))
                    ->render(fn(Departament $departament) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.subdepartaments.departaments.edit', $departament->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm('Элемент будет удален')
                                ->method('remove', [
                                    'id' => $departament->id,
                                ]),
                        ])),

                TD::make('id', '#')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort()
                    ->defaultHidden()
                    ->width(100),

                TD::make('name', 'Название')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200)
                    ->render(
                        fn(Departament $departament) => Link::make($departament->name)
                            ->route('platform.subdepartaments.departaments', ['filter' => ['parent_id' => [$departament->id]]])
                    ),

                TD::make('inn', 'ИНН')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('rating', 'Рейтинг')
                    ->sort()
                    ->width(200),

                TD::make('sort', 'Сортировка')
                    ->sort()
                    ->width(200),

                TD::make('departament_type_id', 'Тип')
                    ->sort()
                    ->width(200)
                    ->render(function (Departament $departament) {
                        try {
                            return $this->departamentTypes->find($departament->departament_type_id)->name;
                        } catch (Throwable) {
                            return '-';
                        }
                    }),

                TD::make('parent_id', 'Родительское учреждение')
                    ->filter(TD::FILTER_SELECT, $this->departamentOptions)
                    ->sort()
                    ->width(200)
                    ->render(function (Departament $departament) {
                        try {
                            return $departament->parent->name;
                        } catch (Throwable) {
                            return '-';
                        }
                    }),

                TD::make('district_id', 'Район')
                    ->sort()
                    ->width(200)
                    ->render(function (Departament $departament) {
                        try {
                            return $this->districts->find($departament->district_id)->name;
                        } catch (Throwable) {
                            return '-';
                        }
                    }),

                TD::make('show_in_dashboard', 'Показывать в дашборде')
                    ->filter(TD::FILTER_SELECT, [true => 'Да', false => 'Нет'])
                    ->sort()
                    ->width(200)
                    ->render(function (Departament $departament) {
                        return $departament->show_in_dashboard ? 'Да' : 'Нет';
                    }),

                TD::make('created_at', 'Создано')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort()
                    ->width(200),

                TD::make('updated_at', 'Обновлено')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort()
                    ->width(200),
            ]),

            Layout::table('users', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn(User $user) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.subdepartaments.users.edit', $user->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                                ->method('removeUser', ['id' => $user->id]),
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
                            return empty(BotUser::where('phone', $user->phone)->count())
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
            ]),
        ];
    }

    public function remove(Request $request): void
    {
        Departament::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }

    public function removeUser(Request $request): void
    {
        User::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }
}
