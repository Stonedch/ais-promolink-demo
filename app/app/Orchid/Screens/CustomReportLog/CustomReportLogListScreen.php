<?php

declare(strict_types=1);

namespace App\Orchid\Screens\CustomReportLog;

use App\Enums\CustomReportLogType;
use App\Models\CustomReportLog;
use App\Orchid\Components\DateTimeRender;
use Exception;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class CustomReportLogListScreen extends Screen
{
    public function query(): iterable
    {
        throw_if(config('app.custom_reports') == false, new Exception('Закрытый доступ!'));

        return [
            'customReportLogs' => CustomReportLog::filters()->defaultSort('id', 'desc')->paginate(100),
        ];
    }

    public function name(): ?string
    {
        return 'Лог загружаемых документов';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.custom-reports.base',
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('customReportLogs', [
                TD::make('id', '#')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort()
                    ->defaultHidden(),

                TD::make('type', 'Тип')
                    ->filter(TD::FILTER_SELECT, CustomReportLogType::options())
                    ->sort()
                    ->render(fn(CustomReportLog $log) => CustomReportLogType::from($log->type)->name()),

                TD::make('message', 'Сообщение')
                    ->filter()
                    ->sort(),

                TD::make('custom_report_type_id', 'Тип загружаемого документа')
                    ->filter()
                    ->sort(),

                TD::make('custom_report_id', 'Загружаемый документ')
                    ->filter()
                    ->sort(),

                TD::make('user_id', 'Пользователь')
                    ->filter()
                    ->sort(),

                TD::make('filepath', 'Путь к документу')
                    ->filter()
                    ->sort(),

                TD::make('template_filepath', 'Путь к шаблону')
                    ->filter()
                    ->sort(),

                TD::make('created_at', 'Создано')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort(),
            ]),
        ];
    }
}
