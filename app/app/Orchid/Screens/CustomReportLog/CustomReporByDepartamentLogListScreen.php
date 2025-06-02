<?php

declare(strict_types=1);

namespace App\Orchid\Screens\CustomReportLog;

use App\Models\CustomReportLog;
use App\Models\Departament;
use App\Orchid\Components\DateTimeRender;
use Exception;
use Illuminate\Support\Facades\Response;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Modal;
use Orchid\Support\Facades\Layout;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Support\Facades\Storage;


class CustomReporByDepartamentLogListScreen extends Screen
{
    public function query(): iterable
    {
        throw_if(config('app.custom_reports') == false, new Exception('Закрытый доступ!'));

        $departaments = Departament::whereHas('customReports')->with('customReports')->filters()->paginate();

        return [
            'departaments' => $departaments,
        ];
    }

    public function name(): ?string
    {
        return 'Лог загружаемых документов по учреждениям';
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
            Layout::modal('reportsModal', [
                Layout::table('customReportLogs', [
                    TD::make('id', '#')->sort(),

                    TD::make('created_at', 'Создано')
                        ->usingComponent(DateTimeRender::class)->width(200),

                    TD::make('message', 'Сообщение')->width(200),

                    TD::make('customReportType.title', 'Тип документа')
                        ->render(function ($log) {
                            return $log->customReportType->title ?? '—';
                        })->width(200),

                    TD::make('download', 'Скачать')
                        ->render(function ($log) {
                            $report = $log->customReport;
                            $attachment = $report?->attachment;

                            if (!$attachment) {
                                return '-';
                            }

                            return Link::make('Скачать')
                                ->icon('cloud-download')
                                ->href('/admin/custom-report-logs/by-departaments/download/' . $attachment->id)
                                ->target('_blank');
                        })->width(200),
                ]),
            ])->async('asyncShowReportsModal')->size(Modal::SIZE_LG),
            new class extends Table {
                protected $target = 'departaments';

                public function columns(): array
                {
                    return [
                        TD::make('id', 'ID')
                            ->sort()
                            ->filter(TD::FILTER_NUMERIC),

                        TD::make('name', 'Учреждение')
                            ->sort()
                            ->filter(TD::FILTER_TEXT)
                            ->render(function ($departament) {
                                return ModalToggle::make($departament->name)
                                    ->modal('reportsModal')
                                    ->modalTitle("Отчеты учреждения: {$departament->name}")
                                    ->asyncParameters([
                                        'departament_id' => $departament->id,
                                    ])
                                    ->icon('list');
                            }),

                        TD::make('reports_count', 'Количество загруженных отчетов')
                            ->render(function ($departament) {
                                return $departament->customReports->count();
                            }),

                        TD::make('last_report_date', 'Дата последнего загруженного документа')
                            ->render(function ($departament) {
                                $last = $departament->customReports->sortByDesc('created_at')->first();
                                return $last ? $last->created_at->format('d.m.Y H:i') : '-';
                            }),
                    ];
                }
            },
        ];
    }

    public function asyncShowReportsModal(int $departament_id): iterable
    {
        $reports = CustomReportLog::with('customReportType')->whereHas('customReport.user', function ($q) use ($departament_id) {
            $q->where('departament_id', $departament_id);
        })
            ->with(['customReport'])
            ->orderByDesc('created_at')
            ->get();

        return [
            'customReportLogs' => $reports,
        ];
    }

    public function download(int $id)
    {
        $attachment = Attachment::findOrFail($id);

        $disk = $attachment->disk ?? 'public';
        $path = $attachment->path;
        $fullPath = Storage::disk($disk)->path($path);

        if (!Storage::disk($disk)->exists($path)) {
            \Orchid\Support\Facades\Toast::error('Файл не найден');
            return redirect()->back();
        }

        return Response::download($fullPath . $attachment->name . '.' . $attachment->extension);
    }
}
