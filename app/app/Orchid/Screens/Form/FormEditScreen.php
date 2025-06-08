<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Form;

use App\Enums\FormStructureType;
use App\Helpers\PhoneNormalizer;
use App\Models\Collection;
use App\Models\DepartamentType;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormDepartamentType;
use App\Models\FormGroup;
use App\Models\User;
use App\Orchid\Fields\Button;
use App\Orchid\Fields\FormItemMatrix;
use Exception;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

// =~~~~~~~~~~~~~~~~~~~~=
// | ~  ~  <>[ ~~ ~ <>[ |
// | `` <>< ~`` <>[ ``~ |
// |  ```~   <>[ ~`` ~  |
// ======================

class FormEditScreen extends Screen
{
    public ?Form $form = null;
    public ?array $checkers = null;
    public ?array $structure = null;
    public ?SupportCollection $groups = null;

    protected static int $lastGeneralSort = -100000;

    public function query(Form $form): iterable
    {
        $formDepartamentTypes = $form->exists
            ? FormDepartamentType::where('form_id', $form->id)->pluck('departament_type_id')->toArray()
            : new SupportCollection();

        $groups =  $form->exists ? FormGroup::where('form_id', $form->id)->orderBy('sort')->get() : new SupportCollection();

        $groups->map(function (FormGroup $formGroup) use ($groups) {
            if (empty($formGroup->parent_id) == false) {
                $formGroup->parent = $groups->where('id', $formGroup->parent_id)->first()->slug;
            }

            $formGroup->general_id = $formGroup->id;
            $formGroup->general_name = $formGroup->name;
            $formGroup->general_group_id = $formGroup->parent_id;
            $formGroup->general_type = FormStructureType::GROUP->value;
            $formGroup->general_sort = $formGroup->sort;

            $formGroup->group_is_multiple = $formGroup->is_multiple;

            return $formGroup;
        });

        $fields = $form->exists
            ? Field::where('form_id', $form->id)->orderBy('sort')->get()
            : new SupportCollection();

        $fields->map(function (Field $field) use ($groups, $fields) {
            $field->general_id = $field->id;
            $field->general_name = $field->name;
            $field->general_group_id = $field->group_id;
            $field->general_type = FormStructureType::FIELD->value;

            $field->general_sort = self::fixGeneralSort($field->sort, $groups);
            $field->general_sort = self::fixGeneralSort($field->general_sort, $fields, $field->id);

            $field->field_type = $field->type;
            $field->field_collection_id = $field->collection_id;
            $field->field_checker_user_id = $field->checker_user_id;

            return $field;
        });

        $structure = $fields->merge($groups)->sortBy('general_sort')->keyBy('general_sort')->toArray();

        $checkers = [];

        User::whereHas('roles', fn(Builder $query) => $query->where('slug', 'checker'))->get()->map(function (User $user) use (&$checkers) {
            $options[$user->id] = '#' . $user->id . ', ' . $user->getFullname() . ', ' . PhoneNormalizer::humanizePhone($user->phone);
        });

        if ($form->exists) {
            $form->departament_types = $formDepartamentTypes;
        }

        return [
            'form' => $form,
            'groups' => $groups,
            'departament_types' => $formDepartamentTypes,
            'structure' => $structure,
            'checkers' => $checkers,
        ];
    }

    protected static function fixGeneralSort(int $sort, SupportCollection $structure, int $id = null): int
    {
        $findeds = $structure
            ->where('sort', $sort);

        if (empty($id) == false) {
            $findeds = $findeds->where('id', '!=', $id);
        }

        if ($findeds->count()) {
            $sort = self::$lastGeneralSort;
            self::$lastGeneralSort += 100;
        }

        return $sort;
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
                ->color(Color::SUCCESS)
                ->icon('bs.check')
                ->method('save'),

            Button::make('Удалить')
                ->color(Color::DANGER)
                ->icon('bs.trash')
                ->method('remove')
                ->confirm('Форма будет удалена')
                ->canSee($this->form->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::block(Layout::rows([
                Group::make([
                    Input::make('form.name')
                        ->required()
                        ->help('Заголовок события на заполнение формы')
                        ->title('Название'),

                    Select::make('form.type')
                        ->required()
                        ->options(Form::$TYPES)
                        ->help('Тип визуализации формы')
                        ->title('Тип формы'),

                    Select::make('form.periodicity')
                        ->required()
                        ->options(Form::$PERIODICITIES)
                        ->help('Периодичность повторения события на заполнение формы')
                        ->title('Периодичность'),
                ]),

                Group::make([
                    Input::make('form.deadline')
                        ->help('Количество дней до блокировки заполнения формы')
                        ->title('Количество дней до просрочки'),

                    Select::make('form.form_category_id')
                        ->empty('-')
                        ->options(FormCategory::pluck('name', 'id'))
                        ->help('Категория/директория формы в которой будет показываться событие на заполнение')
                        ->title('Категория'),

                    Input::make('form.sort')
                        ->title('Сортировка'),
                ]),

                Select::make('form.departament_types')
                    ->options(fn() => DepartamentType::pluck('name', 'id')->toArray())
                    ->multiple()
                    ->title('Учреждения'),


                Group::make([
                    CheckBox::make('form.is_active')
                        ->sendTrueOrFalse()
                        ->help('Показатель отвечающий за возможность редактирования и вывода в списках события данной формы')
                        ->title('Активность'),

                    CheckBox::make('form.is_editable')
                        ->sendTrueOrFalse()
                        ->help('Показатель отвечающий за возможность редактирования уже подтвержденного события формы')
                        ->title('Возможность редактировать'),
                ]),

                CheckBox::make('form.by_initiative')
                    ->sendTrueOrFalse()
                    ->title('По инициативе'),
            ]))
                ->title('Базовые настройки')
                ->vertical(true)
                ->commands([
                    Button::make('Сохранить')
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->method('save'),
                ]),

            Layout::block(Layout::rows([
                FormItemMatrix::make('structure')
                    ->columns([
                        '#' => 'general_id',
                        'Заголовок' => 'general_name',
                        'Группа' => 'general_group_id',
                        'Тип' => 'general_type',
                        'Сортировка' => 'general_sort',
                        'Тип поля' => 'field_type',
                        'Коллекция поля' => 'field_collection_id',
                        'Проверяющий поля' => 'field_checker_user_id',
                        'Множественная группа' => 'group_is_multiple',
                        'Слаг' => 'general_slug',
                        'Ключ рассылки' => 'event_key',
                    ])
                    ->withHiddenColumns()
                    ->hiddenColumns([
                        'general_id',
                        'field_type',
                        'field_collection_id',
                        'field_checker_user_id',
                        'group_is_multiple',
                        'general_slug',
                        'event_key',
                    ])
                    ->fields([
                        'general_id' => Input::make()->hidden()->class('form-control --modal-hidden'),
                        'general_name' => Input::make(),
                        'general_group_id' => Select::make()->empty('-')->options(FormGroup::where('form_id', $this->form->id)->pluck('name', 'id')),
                        'general_type' => Select::make()->empty('-')->options(FormStructureType::getSelectOptions())->class('form-control --select-parent'),
                        'general_sort' => Input::make()->type('number')->class('form-control _sortable'),
                        'field_type' => Select::make()->empty('-')->options(Field::$TYPES)->hidden()->class('form-control --select-field-type'),
                        'field_collection_id' => Select::make()->options(fn() => Collection::pluck('name', 'id'))->empty('-')->hidden()->class('form-control --select-field-type'),
                        'field_checker_user_id' => Select::make()->options($this->checkers)->empty('-')->hidden()->class('form-control --select-field-type'),
                        'group_is_multiple' => Select::make()->options([1 => 'Да'])->empty('Нет')->hidden()->class('form-control --select-group-type'),
                        'general_slug' => Input::make()->class("form-control _sluggable --modal-hidden")->hidden(),
                        'event_key' => Input::make()->hidden()->placeholder('{ключ}'),
                    ]),
            ]))
                ->title('Структура')
                ->canSee($this->form->exists)
                ->vertical(true)
                ->commands([
                    Button::make('Настроить сводную')
                        ->turbo(false)
                        ->icon('bs.code-square')
                        ->data([
                            'form-id' => $this->form->id
                        ])
                        ->canSee($this->form->type == 300)
                        ->class('btn btn-dark _open-modal-structure'),

                    Button::make('Превью')
                        ->turbo(false)
                        ->icon('bs.back')
                        ->data([
                            'form-id' => $this->form->id
                        ])
                        ->class('btn btn-dark _open-modal-structure'),

                    Button::make('Сохранить')
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->method('save'),
                ]),
        ];
    }

    public function save(Request $request, Form $form)
    {
        $form->fill($request->input('form', []))->save();

        FormDepartamentType::query()
            ->where('form_id', $form->id)
            ->get()
            ->map(function (FormDepartamentType $formDepartamentType) use ($request) {
                if (in_array($formDepartamentType->id, $request->input('departament_types', [])) == false) {
                    $formDepartamentType->delete();
                }
            });

        foreach ($request->input('form.departament_types', []) as $id) {
            (new FormDepartamentType())->fill([
                'form_id' => $form->id,
                'departament_type_id' => $id,
            ])->save();
        }

        $structure = collect($request->input('structure', []));

        $fields = Field::query()->where('form_id', $form->id)->get()->keyBy('id');
        $requestedFields = [];

        $structure->where('general_type', FormStructureType::FIELD->value)->map(function ($requestedField) use (&$requestedFields, $form, $fields) {
            try {
                $field = $fields->get($requestedField['general_id']);

                if (empty($field)) {
                    $field = new Field();
                }

                $field->fill([
                    'form_id' => $form->id,
                    'name' => @$requestedField['general_name'] ?: null,
                    'group_id' => @$requestedField['general_group_id'] ?: null,
                    'sort' => @$requestedField['general_sort'] ?: null,
                    'type' => @$requestedField['field_type'] ?: null,
                    'collection_id' => @$requestedField['field_collection_id'],
                    'checker_user_id' => @$requestedField['field_checker_user_id'],
                    'event_key' => @$requestedField['event_key'],
                ])->save();

                $requestedFields[$field->id] = $requestedField;
            } catch (Throwable | Exception) {
                return;
            }
        });

        $fields->whereNotIn('id', array_keys($requestedFields))
            ->map(fn(Field $field) => $field->delete());

        $groups = FormGroup::query()->where('form_id', $form->id)->get()->keyBy('id');
        $requestedGroups = [];

        $structure->where('general_type', FormStructureType::GROUP->value)->map(function ($requestedGroup) use (&$requestedGroups, $form, $groups) {
            try {
                $group = $groups->get($requestedGroup['general_id']);

                if (empty($group)) {
                    $group = new FormGroup();
                }

                $group->fill([
                    'form_id' => $form->id,
                    'name' => @$requestedGroup['general_name'] ?: null,
                    'parent_id' => @$requestedGroup['general_group_id'],
                    'slug' => @$requestedGroup['general_slug'],
                    'sort' => @$requestedGroup['general_sort'] ?: null,
                    'is_multiple' => @$requestedGroup['group_is_multiple'] ?: false,
                ])->save();

                $requestedGroups[$group->id] = $requestedGroup;
            } catch (Throwable | Exception) {
                return;
            }
        });

        $groups->whereNotIn('id', array_keys($requestedGroups))
            ->map(fn(FormGroup $group) => $group->delete());

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
