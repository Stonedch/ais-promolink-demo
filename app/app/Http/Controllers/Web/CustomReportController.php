<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Http\Controllers\Controller;
use App\Models\CustomReport;
use App\Models\CustomReportLog;
use App\Models\CustomReportType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use OCILob;
use Orchid\Attachment\Models\Attachment;
use Throwable;

class CustomReportController extends Controller
{
    protected array $views = [
        'index' => 'web.custom-reports.index',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        try {
            $user = $request->user();

            throw_if(
                empty($user),
                new HumanException('Ошибка авторизации! Номер ошибки: #1000')
            );

            throw_if(
                $user->hasAnyAccess(['platform.custom-reports.loading']) == false,
                new HumanException('Ошибка доступа к функционалу кастомного отчета!')
            );

            $response = [];

            $response['logs'] = CustomReportLog::query()
                ->where('user_id', $user->id)
                ->whereNotNull('custom_report_type_id')
                ->whereNotNull('custom_report_id')
                ->orderBy('created_at', 'DESC')
                ->get()
                ->map(function (CustomReportLog $log) {
                    if ($log->message == 'loaded') {
                        $log->message = 'Документ загружен';
                    }

                    if ($log->message == 'is ready') {
                        $log->message = 'Документ полностью обработан';
                    }

                    return $log;
                });

            $response['customReports'] = CustomReport::query()
                ->where('user_id', $user->id)
                ->get()
                ->keyBy('id');

            $response['customReportTypes'] = CustomReportType::query()
                ->whereIn('id', $response['customReports']->pluck('custom_report_type_id'))
                ->get()
                ->keyBy('id');

            return view($this->views['index'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors(['msg' => $e->getMessage()]);
        } catch (Throwable $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors(['Внутренняя ошибка']);
        }
    }

    public function downloadTemplate(Request $request)
    {
        $user = $request->user();
        throw_if(empty($user));
        throw_if($user->hasAnyAccess(['platform.custom-reports.loading']) == false);
        $type = CustomReportType::find($request->input('id'));
        throw_if(empty($type));
        $template = Attachment::find($type->attachment_id);
        throw_if(empty($template));
        $path = storage_path("app/{$template->disk}/{$template->path}{$template->name}.{$template->extension}");
        return response()->download($path, "{$type->title}.{$template->extension}");
    }
}
