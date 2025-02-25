<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Departament;

use App\Models\Collection;
use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\District;
use App\Orchid\Components\DateTimeRender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

class DepartamentListScreen extends Screen
{
    public $departamentTypes;
    public $districts;

    public function query(): iterable
    {
        $departaments = Departament::filters()->defaultSort('id', 'desc')->paginate(50);

        return [
            'departaments' => $departaments,
            'departamentTypes' => $departaments->isNotEmpty()
                ? DepartamentType::whereIn('id', $departaments->pluck('departament_type_id') ?: [])->get()
                : new Collection(),
            'districts' => $departaments->isNotEmpty()
                ? District::whereIn('id', $departaments->pluck('district_id'))->get()
                : new Collection(),
        ];
    }

    public function name(): ?string
    {
        return 'Учреждения';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.departaments.list',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus')
                ->href(route('platform.departaments.create'))
                ->canSee(Auth::user()->hasAccess('platform.departaments.edit')),
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
                                ->route('platform.departaments.edit', $departament->id)
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
                    ->width(200),

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
        ];
    }

    public function remove(Request $request): void
    {
        Departament::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }
}
