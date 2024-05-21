<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Form;

use App\Models\Form;
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

class FormListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'forms' => Form::filters()->defaultSort('id', 'desc')->paginate(),
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
            Link::make(__('Add'))
                ->icon('bs.plus')
                ->href(route('platform.forms.create'))
                ->canSee(Auth::user()->hasAccess('platform.forms.edit')),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('forms', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->canSee(Auth::user()->hasAccess('platform.forms.edit'))
                    ->render(fn (Form $form) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
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

                TD::make('periodicity', 'Периодичность')
                    ->sort()
                    ->width(200)
                    ->render(fn (Form $form) => $form::$PERIODICITIES[$form->periodicity]),

                TD::make('type', 'Тип')
                    ->sort()
                    ->width(200)
                    ->render(fn (Form $form) => $form::$TYPES[$form->type]),

                TD::make('is_active', 'Активность')
                    ->sort()
                    ->width(150)
                    ->render(fn (Form $form) => $form->is_active ? 'Да' : 'Нет'),

                TD::make('is_editable', 'Возможность редактирования')
                    ->sort()
                    ->width(250)
                    ->render(fn (Form $form) => $form->is_editable ? 'Да' : 'Нет'),

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
        Form::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }
}
