<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Departament;

use App\Models\Departament;
use App\Models\DepartamentType;
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

class DepartamentListScreen extends Screen
{
    public $departamentTypes;

    public function query(): iterable
    {
        return [
            'departaments' => Departament::filters()->defaultSort('id', 'desc')->paginate(),
            'departamentTypes' => DepartamentType::where('id', Departament::pluck('departament_type_id'))->get()
        ];
    }

    public function name(): ?string
    {
        return 'Видомства';
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
                    ->render(fn (Departament $departament) => DropDown::make()
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

                TD::make('departament_type_id', 'Тип')
                    ->sort()
                    ->width(200)
                    ->render(fn (Departament $departament) => $this->departamentTypes->find($departament->departament_type_id)->name),

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
