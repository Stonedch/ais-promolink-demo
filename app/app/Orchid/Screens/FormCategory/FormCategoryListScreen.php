<?php

declare(strict_types=1);

namespace App\Orchid\Screens\FormCategory;

use App\Models\FormCategory;
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

class FormCategoryListScreen extends Screen
{
    public function query(): iterable
    {
        $formCategories = FormCategory::query()
            ->filters()
            ->defaultSort('id', 'desc')
            ->paginate();

        return [
            'formCategories' => $formCategories,
        ];
    }

    public function name(): ?string
    {
        return 'Категории форм';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.form-categories.list',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus')
                ->href(route('platform.form-categories.create'))
                ->canSee(Auth::user()->hasAccess('platform.form-categories.edit')),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('formCategories', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->canSee(Auth::user()->hasAccess('platform.form-categories.edit'))
                    ->render(fn (FormCategory $formCategory) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.form-categories.edit', $formCategory->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm('Элемент будет удален')
                                ->method('remove', [
                                    'id' => $formCategory->id,
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

                TD::make('sort', 'Сортировка')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort()
                    ->width(200),

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
        FormCategory::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }
}
