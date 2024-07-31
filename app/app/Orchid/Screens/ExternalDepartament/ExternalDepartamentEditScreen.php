<?php

declare(strict_types=1);

namespace App\Orchid\Screens\ExternalDepartament;

use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\District;
use App\Models\ExternalDepartament;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ExternalDepartamentEditScreen extends Screen
{
    public $externalDepartament;

    public function query(ExternalDepartament $externalDepartament): iterable
    {
        return [
            'externalDepartament' => $externalDepartament,
        ];
    }

    public function name(): ?string
    {
        return 'Управление Внешним Учреждениями';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.external-departaments.edit',
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
                ->canSee($this->externalDepartament->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('externalDepartament.orgname')
                    ->required()
                    ->title('Наименование организации'),

                Input::make('externalDepartament.orgsokrname')
                    ->title('Сокращенное наименование организации'),

                Input::make('externalDepartament.orgpubname')
                    ->title('Полное наименование организации'),

                Input::make('externalDepartament.type')
                    ->title('Тип'),

                Input::make('externalDepartament.post')
                    ->title('Должность руководителя'),

                Input::make('externalDepartament.rukfio')
                    ->title('ФИО руководителя'),

                Input::make('externalDepartament.orgfunc')
                    ->title('Задача организации'),

                Input::make('externalDepartament.index')
                    ->title('Индекс'),

                Input::make('externalDepartament.region')
                    ->title('Регион'),

                Input::make('externalDepartament.area')
                    ->title('Район'),

                Input::make('externalDepartament.town')
                    ->title('Город'),

                Input::make('externalDepartament.street')
                    ->title('Улица'),

                Input::make('externalDepartament.house')
                    ->title('Дом'),

                Input::make('externalDepartament.latitude')
                    ->title('Широта'),

                Input::make('externalDepartament.longitude')
                    ->title('Долгота'),

                Input::make('externalDepartament.mail')
                    ->title('E-mail'),

                Input::make('externalDepartament.telephone')
                    ->title('Телефон'),

                Input::make('externalDepartament.fax')
                    ->title('Факс'),

                Input::make('externalDepartament.telephonedop')
                    ->title('Доп. телефон'),

                Input::make('externalDepartament.url')
                    ->title('Сайт'),

                Input::make('externalDepartament.okpo')
                    ->title('ОКПО'),

                Input::make('externalDepartament.ogrn')
                    ->title('ОГРН'),

                Input::make('externalDepartament.inn')
                    ->title('ИНН'),

                Input::make('externalDepartament.schedule')
                    ->title('Расписание'),
            ]),
        ];
    }

    public function save(Request $request, ExternalDepartament $externalDepartament)
    {
        $externalDepartament->fill($request->input('externalDepartament', []));
        $externalDepartament->save();
        Toast::info('Успешно сохранено!');
        return redirect()->route('platform.external-departaments.edit', $externalDepartament);
    }

    public function remove(ExternalDepartament $externalDepartament)
    {
        $externalDepartament->delete();
        Toast::info('Успешно удалено');
        return redirect()->route('platform.external-departaments');
    }
}
