<?php

declare(strict_types=1);

namespace App\Orchid\Screens\ExternalDepartament;

use App\Models\ExternalDepartament;
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

class ExternalDepartamentListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'externalDepartaments' => ExternalDepartament::filters()->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Внешние учреждения';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.external-departaments.list',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus')
                ->href(route('platform.external-departaments.create'))
                ->canSee(Auth::user()->hasAccess('platform.external-departaments.edit')),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('externalDepartaments', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->canSee(Auth::user()->hasAccess('platform.external-departaments.edit'))
                    ->render(fn (ExternalDepartament $externalDepartament) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.external-departaments.edit', $externalDepartament->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm('Элемент будет удален')
                                ->method('remove', [
                                    'id' => $externalDepartament->id,
                                ]),
                        ])),

                TD::make('id', '#')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort()
                    ->defaultHidden()
                    ->width(100),

                TD::make('orgname', 'Наименование организации')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('orgsokrname', 'Сокращенное наименование организации')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('orgpubname', 'Полное наименование организации')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('type', 'Тип')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('post', 'Должность руководителя')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('rukfio', 'ФИО руководителя')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('orgfunc', 'Задача организации')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('index', 'Индекс')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('region', 'Регион')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('area', 'Район')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('town', 'Город')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('street', 'Улица')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('house', 'Дом')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('latitude', 'Широта')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('longitude', 'Долгота')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('mail', 'E-mail')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('telephone', 'Телефон')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('fax', 'Факс')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('telephonedop', 'Доп. телефон')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('url', 'Сайт')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('okpo', 'ОКПО')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('ogrn', 'ОГРН')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('inn', 'ИНН')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('schedule', 'Расписание')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

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
        ExternalDepartament::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }
}
