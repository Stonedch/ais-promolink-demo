<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Form;

use App\Helpers\PhoneNormalizer;
use App\Models\Collection;
use App\Models\DepartamentType;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormChecker;
use App\Models\FormDepartamentType;
use App\Models\FormGroup;
use App\Models\User;
use App\Orchid\Fields\FormItemMatrix;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

class FormEditScreen extends Screen
{
    public $form;

    public function query(Form $form): iterable
    {
        $groups =  $form->exists ? FormGroup::where('form_id', $form->id)->orderBy('sort')->get() : null;

        $groups->map(function (FormGroup $formGroup) use ($groups) {
            if (empty($formGroup->parent_id)) return $formGroup;
            $formGroup->parent = $groups->where('id', $formGroup->parent_id)->first()->slug;
        });

        return [
            'form' => $form,
            'departament_types' => $form->exists
                ? FormDepartamentType::where('form_id', $form->id)->pluck('departament_type_id')->toArray()
                : null,
            'fields' => $form->exists
                ? Field::where('form_id', $form->id)->orderBy('sort')->get()
                : null,
            'groups' => $groups,
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

            Layout::block(Layout::rows([
                Matrix::make('groups')
                    ->columns([
                        '#' => 'id',
                        'Родительская группа' => 'parent',
                        'Заголовок' => 'name',
                        'Сортировка' => 'sort',
                        'Слаг' => 'slug',
                    ])
                    ->fields([
                        'id' => Input::make()->hidden(),
                        'parent' => Input::make(),
                        'name' => Input::make(),
                        'sort' => Input::make()->type('number')->class("form-control _sortable"),
                        'slug' => Input::make()->class("form-control _sluggable"),
                    ])
            ]))->title('Группы')->canSee($this->form->exists)->commands([
                Button::make('Сохранить структуру')
                    ->type(Color::BASIC)
                    ->icon('bs.check-circle')
                    ->method('saveGroups')
            ]),

            Layout::rows([
                FormItemMatrix::make('fields')
                    ->columns([
                        '#' => 'id',
                        'Заголовок' => 'name',
                        'Группа' => 'group',
                        'Группа (демо)' => 'group_id',
                        'Тип' => 'type',
                        'Сортировка' => 'sort',
                        'Коллекция' => 'collection_id',
                        'Проверяющий' => 'checker_user_id',
                    ])
                    ->fields([
                        'id' => Input::make()->disabled()->hidden(),
                        'name' => Input::make(),
                        'group' => Input::make(),
                        'group_id' => Select::make()->empty('-')->options(FormGroup::where('form_id', $this->form->id)->pluck('name', 'id')),
                        'type' => Select::make()->options(Field::$TYPES),
                        'sort' => Input::make()->type('number')->class("form-control _sortable"),
                        'collection_id' => Select::make()->options(fn () => Collection::pluck('name', 'id'))->empty('-'),
                        'checker_user_id' => Select::make()->options(function () {
                            $options = [];

                            foreach (User::whereHas('roles', fn (Builder $query) => $query->where('slug', 'checker'))->get() as $user) {
                                $options[$user->id] = '#' . $user->id . ', ' . $user->getFullname() . ', ' . PhoneNormalizer::humanizePhone($user->phone);
                            }

                            return $options;
                        })->empty('-'),
                    ])
                    ->title('Значения'),
            ])->title('Поля')->canSee($this->form->exists),
        ];
    }

    public function saveGroups(Request $request, Form $form)
    {
        $groups = collect($request->input('groups', []));
        $currentParent = null;
        $slugges = [];
        $identifiers = [];

        while (true) {
            foreach ($groups->where('parent_id', $currentParent) as $group) {
                try {
                    $formGroup = empty($group['id']) == false
                        ? FormGroup::find($group['id'])
                        : new FormGroup();

                    $parent = empty($group['parent']) == false
                        ? $identifiers[$group['parent']]
                        : null;

                    $formGroup->fill([
                        'name' => $group['name'],
                        'slug' => $group['slug'],
                        'sort' => $group['sort'],
                        'form_id' => $form->id,
                        'parent_id' => $parent,
                    ])->save();

                    $slugges[] = $group['slug'];
                    $identifiers[$group['slug']] = $formGroup->id;
                } catch (Throwable) {
                    continue;
                }
            }

            if (empty($slugges)) break;

            $currentParent = array_pop($slugges);
        }

        FormGroup::query()
            ->where('form_id', $form->id)
            ->whereNotIn('id', array_values($identifiers))
            ->get()
            ->map(fn (FormGroup $formGroup) => $formGroup->delete());
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

        $checkers = FormChecker::where('form_id', $form->id)->get();

        if ($isFieldsReinit) {
            $fields->map(fn (Field $field) => $field->delete());
            $checkers->map(fn (FormChecker $checker) => $checker->delete());

            foreach ($requestedFields as $item) {
                try {
                    $field = new Field();
                    $field->fill($item);
                    $field->form_id = $form->id;
                    $field->sort = $field->sort ?: 100;
                    $field->save();

                    if (empty($item['checker_user_id']) == false) {
                        (new FormChecker())->fill([
                            'user_id' => $item['checker_user_id'],
                            'form_id' => $form->id,
                            'field_id' => $field->id,
                        ])->save();
                    }
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
