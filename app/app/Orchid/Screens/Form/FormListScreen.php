<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Form;

use App\Exceptions\HumanException;
use App\Helpers\FormExporter;
use App\Models\DepartamentType;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormFieldBlocked;
use App\Models\FormGroup;
use App\Orchid\Components\DateTimeRender;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use PHPUnit\Event\Code\Throwable;

class FormListScreen extends Screen
{
    public ?Collection $categories = null;

    public function query(): iterable
    {
        $periodicityForms = Form::query()
            ->with('departamentTypes')
            ->where('periodicity', '<>', 50)
            ->filters()
            ->defaultSort('id', 'desc')
            ->paginate();

        $notPeriodicityForms = Form::query()
            ->with('departamentTypes')
            ->where('periodicity', '=', 50)
            ->filters()
            ->defaultSort('id', 'desc')
            ->paginate();

        $categories = FormCategory::query()
            ->whereIn(
                'id',
                $periodicityForms->pluck('form_category_id')->merge(
                    $notPeriodicityForms->pluck('form_category_id')->toArray()
                )
            )
            ->get();

        return [
            'periodicityForms' => $periodicityForms,
            'notPeriodicityForms' => $notPeriodicityForms,
            'categories' => $categories,
        ];
    }

    public function name(): ?string
    {
        return 'Формы';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.forms.list',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Копировать форму')
                ->icon('bs.copy')
                ->modal('copyFormModal')
                ->modalTitle('Копирование формы')
                ->method('copyForm'),

            Link::make(__('Add'))
                ->icon('bs.plus')
                ->href(route('platform.forms.create'))
                ->canSee(Auth::user()->hasAccess('platform.forms.edit')),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::modal('copyFormModal', [Layout::rows([
                Select::make('copyFormModal.form_id')
                    ->options(fn() => Form::pluck('name', 'id'))
                    ->title('Форма')
                    ->required(),
                Input::make('copyFormModal.name')
                    ->title('Название')
                    ->required(),
            ])]),

            Layout::table('periodicityForms', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->canSee(Auth::user()->hasAccess('platform.forms.edit'))
                    ->render(fn(Form $form) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Button::make('Выгрузить')
                                ->icon('bs.cloud-download')
                                ->confirm('Подтвердите свои действия')
                                ->method('exportArchive', [
                                    'id' => $form->id
                                ])
                                ->turbo(false),

                            Link::make(__('Edit'))
                                ->route('platform.forms.edit', $form->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm('Элемент будет удален')
                                ->method('remove', [
                                    'id' => $form->id,
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

                TD::make('', 'Учреждения')
                    ->width(200)
                    ->render(function (Form $form) {
                        try {
                            return implode(
                                ';<br> ',
                                $form->departamentTypes->pluck('name')->toArray()
                            );
                        } catch (Throwable) {
                            return '-';
                        }
                    }),

                TD::make('periodicity', 'Периодичность')
                    ->sort()
                    ->width(200)
                    ->render(fn(Form $form) => $form::$PERIODICITIES[$form->periodicity]),

                TD::make('type', 'Тип')
                    ->sort()
                    ->width(200)
                    ->render(fn(Form $form) => $form::$TYPES[$form->type]),

                TD::make('is_active', 'Активность')
                    ->sort()
                    ->width(150)
                    ->render(fn(Form $form) => $form->is_active ? 'Да' : 'Нет'),

                TD::make('is_editable', 'Возможность редактирования')
                    ->sort()
                    ->width(250)
                    ->render(fn(Form $form) => $form->is_editable ? 'Да' : 'Нет'),

                TD::make('sort', 'Сортировка')
                    ->sort()
                    ->width(250),

                TD::make('form_category_id', 'Категория')
                    ->sort()
                    ->width(200)
                    ->render(function (Form $form) {
                        try {
                            return $this->categories->where('id', $form->form_category_id)->first()->name;
                        } catch (Exception) {
                            return null;
                        }
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
            ])->title('Периодичные формы'),

            Layout::table('notPeriodicityForms', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->canSee(Auth::user()->hasAccess('platform.forms.edit'))
                    ->render(fn(Form $form) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Button::make('Выгрузить')
                                ->icon('bs.cloud-download')
                                ->confirm('Подтвердите свои действия')
                                ->method('exportArchive', [
                                    'id' => $form->id
                                ])
                                ->turbo(false),

                            Link::make(__('Edit'))
                                ->route('platform.forms.edit', $form->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm('Элемент будет удален')
                                ->method('remove', [
                                    'id' => $form->id,
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

                TD::make('', 'Учреждения')
                    ->width(200)
                    ->render(function (Form $form) {
                        try {
                            return implode(
                                ';<br> ',
                                $form->departamentTypes->pluck('name')->toArray()
                            );
                        } catch (Throwable) {
                            return '-';
                        }
                    }),

                TD::make('periodicity', 'Периодичность')
                    ->sort()
                    ->width(200)
                    ->render(fn(Form $form) => $form::$PERIODICITIES[$form->periodicity]),

                TD::make('type', 'Тип')
                    ->sort()
                    ->width(200)
                    ->render(fn(Form $form) => $form::$TYPES[$form->type]),

                TD::make('is_active', 'Активность')
                    ->sort()
                    ->width(150)
                    ->render(fn(Form $form) => $form->is_active ? 'Да' : 'Нет'),

                TD::make('is_editable', 'Возможность редактирования')
                    ->sort()
                    ->width(250)
                    ->render(fn(Form $form) => $form->is_editable ? 'Да' : 'Нет'),

                TD::make('sort', 'Сортировка')
                    ->sort()
                    ->width(250),

                TD::make('form_category_id', 'Категория')
                    ->sort()
                    ->width(200)
                    ->render(function (Form $form) {
                        try {
                            return $this->categories->where('id', $form->form_category_id)->first()->name;
                        } catch (Exception) {
                            return null;
                        }
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
            ])->title('Разовые формы'),
        ];
    }

    public function remove(Request $request): void
    {
        Form::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }

    public function exportArchive(int $id)
    {
        return response()->download(FormExporter::exportArchiveBy(Form::find($id)));
    }

    public function copyForm(Request $request): void
    {
        try {
            $form = Form::find($request->input('copyFormModal.form_id'));
            $name = $request->input('copyFormModal.name');

            throw_if(empty($form), new HumanException('Форма не найдена'));
            throw_if(empty($name), new HumanException('Пожалуйста, укажите название новой формы'));

            $copiedForm = $form->replicate();
            $copiedForm->fill(['name' => $name])->save();

            Field::query()
                ->where('form_id', $form->id)
                ->get()
                ->map(function (Field $field) use ($copiedForm, $form) {
                    $copiedField = $field->replicate();
                    $copiedField->fill(['form_id' => $copiedForm->id])->save();

                    FormFieldBlocked::query()
                        ->where('form_id', $form->id)
                        ->where('field_id', $field->id)
                        ->get()
                        ->map(function (FormFieldBlocked $formFieldBlocked) use ($copiedForm, $copiedField) {
                            $formFieldBlocked->replicate()->fill([
                                'form_id' => $copiedForm->id,
                                'field_id' => $copiedField->id,
                            ])->save();
                        });
                });

            FormGroup::query()
                ->where('form_id', $form->id)
                ->get()
                ->map(function (FormGroup $formGroup) use ($copiedForm) {
                    $formGroup->replicate()->fill(['form_id' => $copiedForm->id])->save();
                });

            FormGroup::query()
                ->where('form_id', $copiedForm->id)
                ->get()
                ->map(function (FormGroup $formGroup) use ($copiedForm) {
                    if ($formGroup->parent_id) {
                        $oldParentGroup = FormGroup::find($formGroup->parent_id);
                        $newParentGroup = FormGroup::query()
                            ->where('form_id', $copiedForm->id)
                            ->where('slug', $oldParentGroup->slug)
                            ->first();

                        $formGroup->parent_id = $newParentGroup->id;
                        $formGroup->save();
                    }
                });

            Field::query()
                ->where('form_id', $copiedForm->id)
                ->get()
                ->map(function (Field $field) use ($copiedForm) {
                    if ($field->group_id) {
                        $oldParentGroup = FormGroup::find($field->group_id);
                        $newParentGroup = FormGroup::query()
                            ->where('form_id', $copiedForm->id)
                            ->where('slug', $oldParentGroup->slug)
                            ->first();

                        $field->group_id = $newParentGroup->id;
                        $field->save();
                    }
                });

            Toast::info('Успешно!');
        } catch (HumanException $e) {
            Toast::error($e->getMessage());
        } catch (Throwable) {
            Toast::error('Ошибка сервера!');
        }
    }
}
