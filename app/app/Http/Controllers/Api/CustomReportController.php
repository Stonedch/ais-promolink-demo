<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HumanException;
use App\Services\Api\Responser;
use App\Http\Controllers\Controller;
use App\Models\CustomReport;
use App\Models\CustomReportType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Orchid\Attachment\File;

class CustomReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            throw_if(config('app.custom_reports') != true, new HumanException('Доступ закрыт!'));

            $user = $request->user();

            throw_if(
                $user->hasAnyAccess(['platform.custom-reports.loading']) == false,
                new HumanException('Ошибка доступа к функционалу кастомного отчета!')
            );

            $customReportTypes = CustomReportType::byUser($user);

            throw_if(
                empty($customReportTypes->where('id', $request->input('custom_report_type_id'))->count()),
                new HumanException('Ошибка доступа к типу кастомного отчета!')
            );

            throw_if(empty($request->file('attachment')), new HumanException('Ошибка получения файла отчета!'));

            $file = new File($request->file('attachment'));
            $attachment = $file->load();

            (new CustomReport())->fill([
                'user_id' => $user->id,
                'custom_report_type_id' => $request->input('custom_report_type_id'),
                'attachment_id' => $attachment->id,
                'worked' => false,
            ])->save();

            return Responser::returnSuccess([]);
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError([$e->getMessage()]);
        }
    }
}
