<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Departament;

use App\Models\CollectionValue;
use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\District;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class DepartamentEditScreen extends Screen
{
    public $departament;

    public function query(Departament $departament): iterable
    {
        return [
            'departament' => $departament,
        ];
    }

    public function name(): ?string
    {
        return 'Управление Учреждениями';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.departaments.edit',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('bs.check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('bs.trash')
                ->method('remove')
                ->canSee($this->departament->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('departament.name')
                    ->required()
                    ->title('Название'),

                Input::make('departament.inn')
                    ->type('number')
                    ->title('ИНН'),

                Input::make('departament.sort')
                    ->type('number')
                    ->title('Сортировка'),

                Select::make('departament.departament_type_id')
                    ->empty('-')
                    ->options(function () {
                        return DepartamentType::pluck('name', 'id');
                    })
                    ->required()
                    ->title('Тип'),


                Select::make('departament.parent_id')
                    ->empty('-')
                    ->options(fn() => Departament::pluck('name', 'id'))
                    ->title('Родительское учреждение'),

                Select::make('departament.district_id')
                    ->empty('-')
                    ->options(function () {
                        return District::pluck('name', 'id');
                    })
                    ->title('Район'),

                Input::make('departament.rating')
                    ->type('double')
                    ->title('Рейтинг'),

                CheckBox::make('departament.show_in_dashboard')
                    ->sendTrueOrFalse()
                    ->title('Показывать в дашборде'),

                Select::make('departament.federation')
                    ->empty('-')
                    ->options(CollectionValue::where('collection_id', 19)->pluck('value', 'value'))
                    ->title('Федерация')
            ]),

            Layout::rows([
                Group::make([
                    Input::make('departament.phone')->title('Телефон'),
                    Input::make('departament.contact_fullname')->title('Контактное лицо'),
                ]),
                Group::make([
                    Input::make('departament.email')->title('E-mail для рассылки'),
                    Input::make('departament.email_fullname')->title('ФИО получателя рассылки'),
                ]),
            ]),
        ];
    }

    public function save(Request $request, Departament $departament)
    {
        $departament->fill($request->input('departament', []));
        $departament->save();
        Toast::info('Успешно сохранено!');
        return redirect()->route('platform.departaments.edit', $departament);
    }

    public function remove(Departament $departament)
    {
        $departament->delete();
        Toast::info('Успешно удалено');
        return redirect()->route('platform.departaments');
    }
}
