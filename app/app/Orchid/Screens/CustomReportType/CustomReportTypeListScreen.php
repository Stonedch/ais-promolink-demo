<?php

declare(strict_types=1);

namespace App\Orchid\Screens\CustomReportType;

use App\Models\CustomReportType;
use App\Orchid\Components\DateTimeRender;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CustomReportTypeListScreen extends Screen
{
    public function query(): iterable
    {
        throw_if(config('app.custom_reports') == false, new Exception('Закрытый доступ!'));

        return [
            'customReportTypes' => CustomReportType::filters()->defaultSort('id', 'desc')->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Типы загружаемых документов';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.custom-reports.base',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus')
                ->href(route('platform.custom-report-types.create')),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('customReportTypes', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->render(fn(CustomReportType $customReportType) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.custom-report-types.edit', $customReportType->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm('Элемент будет удален')
                                ->method('remove', [
                                    'id' => $customReportType->id,
                                ]),
                        ])),

                TD::make('id', '#')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort()
                    ->defaultHidden()
                    ->width(100),

                TD::make('title', 'Название')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),

                TD::make('is_general', 'Общий тип')
                    ->sort()
                    ->width(100)
                    ->render(fn(CustomReportType $type) => $type->is_general ? 'Да' : 'Нет'),

                TD::make('Загружен шаблон')
                    ->render(fn(CustomReportType $type) => $type->attachment_id ? 'Да' : 'Нет'),

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
        CustomReportType::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }
}
