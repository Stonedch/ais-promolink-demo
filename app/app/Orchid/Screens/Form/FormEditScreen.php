<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Form;

use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\Form;
use App\Models\FormDepartamentType;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FormEditScreen extends Screen
{
    public $form;

    public function query(Form $form): iterable
    {
        return [
            'form' => $form,
            'departament_types' => $form->exists
                ? FormDepartamentType::where('form_id', $form->id)->pluck('departament_type_id')->toArray()
                : null,
        ];
    }

    public function name(): ?string
    {
        return 'Управление формами';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.forms.edit',
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
                ->canSee($this->form->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Group::make([
                    Input::make('form.name')
                        ->require()
                        ->title('Название'),

                    Select::make('form.type')
                        ->require()
                        ->options(Form::$TYPES)
                        ->title('Тип формы'),
                ]),

                Group::make([
                    Select::make('form.periodicity')
                        ->require()
                        ->options(Form::$PERIODICITIES)
                        ->title('Периодичность'),

                    Input::make('form.deadline')
                        ->require()
                        ->title('Количество дней до просрочки'),
                ]),

                Group::make([
                    CheckBox::make('form.is_active')
                        ->sendTrueOrFalse()
                        ->title('Активность'),

                    CheckBox::make('form.is_editable')
                        ->sendTrueOrFalse()
                        ->title('Возможность редактировать'),
                ]),
            ])->title('Базовые настройки'),

            Layout::rows([
                Select::make('departament_types')
                    ->options(fn () => DepartamentType::pluck('name', 'id'))
                    ->multiple(),
            ])->title('Ведомства')
        ];
    }

    public function save(Request $request, Form $form)
    {
        $form->fill($request->input('form', []));
        $form->save();

        $formDepartamentTypes = FormDepartamentType::query()->where('form_id', $form->id)->get();
        $requestedDepartamentTypeIdentifiers = collect($request->input('departament_types', []));
        $isDepartmentTypesReinit = false;

        if ($formDepartamentTypes->count() != $requestedDepartamentTypeIdentifiers->count()) {
            $isDepartmentTypesReinit = true;
        } else {
            $isDepartmentTypesReinit = $requestedDepartamentTypeIdentifiers->every(function ($departamentTypeId) use ($formDepartamentTypes) {
                return $formDepartamentTypes->where('departament_type_id', $departamentTypeId)->isEmpty();
            });
        }

        if ($isDepartmentTypesReinit) {
            $formDepartamentTypes->map(function (FormDepartamentType $item) {
                $item->delete();
            });

            foreach ($requestedDepartamentTypeIdentifiers as $id) {
                $item = new FormDepartamentType();

                $item->fill([
                    'form_id' => $form->id,
                    'departament_type_id' => $id,
                ]);

                $item->save();
            }
        }

        Toast::info('Успешно сохранено!');
        return redirect()->route('platform.forms.edit', $form);
    }

    public function remove(Form $form)
    {
        $form->delete();
        Toast::info('Успешно удалено');
        return redirect()->route('platform.forms');
    }
}
