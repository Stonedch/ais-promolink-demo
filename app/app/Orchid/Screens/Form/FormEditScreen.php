<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Form;

use App\Models\Collection;
use App\Models\DepartamentType;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormDepartamentType;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

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
            'fields' => $form->exists
                ? Field::where('form_id', $form->id)->orderBy('sort')->get()
                : null
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

                Group::make([
                    Select::make('form.form_category_id')
                        ->empty('-')
                        ->options(FormCategory::pluck('name', 'id'))
                        ->title('Категория'),

                    Input::make('form.sort')
                        ->title('Сортировка'),
                ]),
            ])->title('Базовые настройки'),

            Layout::rows([
                Select::make('departament_types')
                    ->options(fn () => DepartamentType::pluck('name', 'id'))
                    ->multiple(),
            ])->title('Учреждения'),

            Layout::rows([
                Matrix::make('fields')
                    ->columns([
                        '#' => 'id',
                        'Заголовок' => 'name',
                        'Группа' => 'group',
                        'Тип' => 'type',
                        'Сортировка' => 'sort',
                        'Коллекция' => 'collection_id',
                    ])
                    ->fields([
                        'id' => Input::make()->disabled()->hidden(),
                        'name' => Input::make(),
                        'group' => Input::make(),
                        'type' => Select::make()->options(Field::$TYPES),
                        'sort' => Input::make()->type('number')->class("form-control _sortable"),
                        'collection_id' => Select::make()->options(fn () => Collection::pluck('name', 'id'))->empty('-'),
                    ])
                    ->title('Значения'),
            ])->title('Поля')->canSee($this->form->exists),
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

        $fields = Field::where('form_id', $form->id)->get();
        $requestedFields = collect($request->input('fields', []));
        $isFieldsReinit = true;

        if ($isFieldsReinit) {
            $fields->map(fn (Field $field) => $field->delete());

            foreach ($requestedFields as $item) {
                try {
                    $field = new Field();
                    $field->fill($item);
                    $field->form_id = $form->id;
                    $field->sort = $field->sort ?: 100;
                    $field->save();
                } catch (Throwable $e) {
                    continue;
                }
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
