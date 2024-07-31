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
                    ->title('orgname'),

                Input::make('externalDepartament.orgsokrname')
                    ->title('orgsokrname'),

                Input::make('externalDepartament.orgpubname')
                    ->title('orgpubname'),

                Input::make('externalDepartament.type')
                    ->title('type'),

                Input::make('externalDepartament.post')
                    ->title('post'),

                Input::make('externalDepartament.rukfio')
                    ->title('rukfio'),

                Input::make('externalDepartament.orgfunc')
                    ->title('orgfunc'),

                Input::make('externalDepartament.index')
                    ->title('index'),

                Input::make('externalDepartament.region')
                    ->title('region'),

                Input::make('externalDepartament.area')
                    ->title('area'),

                Input::make('externalDepartament.town')
                    ->title('town'),

                Input::make('externalDepartament.street')
                    ->title('street'),

                Input::make('externalDepartament.house')
                    ->title('house'),

                Input::make('externalDepartament.latitude')
                    ->title('latitude'),

                Input::make('externalDepartament.longitude')
                    ->title('longitude'),

                Input::make('externalDepartament.mail')
                    ->title('mail'),

                Input::make('externalDepartament.telephone')
                    ->title('telephone'),

                Input::make('externalDepartament.fax')
                    ->title('fax'),

                Input::make('externalDepartament.telephonedop')
                    ->title('telephonedop'),

                Input::make('externalDepartament.url')
                    ->title('url'),

                Input::make('externalDepartament.okpo')
                    ->title('okpo'),

                Input::make('externalDepartament.ogrn')
                    ->title('ogrn'),

                Input::make('externalDepartament.inn')
                    ->title('inn'),

                Input::make('externalDepartament.schedule')
                    ->title('schedule'),
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
