<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Collection;

use App\Models\Collection;
use App\Models\Complaint\Complaint;
use App\Models\User;
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

class CollectionListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'collections' => Collection::filters()->defaultSort('id', 'desc')->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Коллекции';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.collections.list',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus')
                ->href(route('platform.collections.create'))
                ->canSee(Auth::user()->hasAccess('platform.collections.edit')),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('collections', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->canSee(Auth::user()->hasAccess('platform.collections.edit'))
                    ->render(fn (Collection $collection) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.collections.edit', $collection->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm('Жалоба будет удалена')
                                ->method('remove', [
                                    'id' => $collection->id,
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
        Collection::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }
}
