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

            $response['customReportTypes'] = CustomReportType::query()
                ->whereIn('id', $response['logs']->pluck('custom_report_type_id'))
                ->get()
                ->keyBy('id');

            $response['customReports'] = CustomReport::query()
                ->whereIn('id', $response['logs']->pluck('custom_report_id'))
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
}
