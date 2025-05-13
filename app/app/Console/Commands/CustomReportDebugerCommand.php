<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CustomReport;
use App\Models\CustomReportLog;
use App\Models\CustomReportType;
use App\Models\User;
use Illuminate\Console\Command;
use Orchid\Attachment\Models\Attachment;

class CustomReportDebugerCommand extends Command
{
    protected $name = 'custom-report-debuger:run';
    protected $signature = 'custom-report-debuger:run {id} {makeReset=0}';
    protected $description = 'Debuger кастомных отчетов';

    public function handle(): void
    {
        $report = CustomReport::find($this->argument('id'));

        if (boolval($this->argument('makeReset'))) {
            $report->worked = false;
            $report->worked_at = null;
            $report->save();
        }

        $this->comment('- report:' . PHP_EOL . implode(PHP_EOL, [
            "-- id: {$report->id}",
            "-- user_id: {$report->id}",
            "-- custom_report_type_id: {$report->custom_report_type_id}",
            "-- attachment_id: {$report->attachment_id}",
            "-- worked: {$report->worked}",
            "-- created_at: {$report->created_at}",
            "-- updated_at: {$report->updated_at}",
            "-- worked_at: {$report->worked_at}",
        ]));

        $this->newLine();

        $reportAttachment = Attachment::find($report->attachment_id);
        $reportAttachmentPath = '/app/storage/app/public/' . $reportAttachment->path . $reportAttachment->name . '.' . $reportAttachment->extension;

        $this->comment('- report attachment filepath: ' . $reportAttachmentPath);

        $this->newLine();

        $reportType = CustomReportType::find($report->custom_report_type_id);

        $this->comment('- report type:' . PHP_EOL . implode(PHP_EOL, [
            "-- id: {$reportType->id}",
            "-- title: {$reportType->title}",
            "-- attachment_id: {$reportType->attachment_id}",
            "-- is_freelance: {$reportType->is_freelance}",
            "-- command: {$reportType->command}",
        ]));

        $this->newLine();

        $user = User::find($report->user_id);

        $this->comment('- user:' . PHP_EOL . implode(PHP_EOL, [
            "-- id: {$user->id}",
            "-- email: {$user->email}",
            "-- phone: {$user->phone}",
            "-- last_name: {$user->last_name}",
            "-- last_name: {$user->first_name}",
            "-- last_name: {$user->middle_name}",
            "-- departament_id: {$user->departament_id}",
            "-- is_active: {$user->is_active}",
        ]));

        $this->newLine();

        $logs = CustomReportLog::query()
            ->where('custom_report_id', $report->id)
            ->get();

        foreach ($logs as $log) {
            $this->comment('- report log:' . PHP_EOL . implode(PHP_EOL, [
                "-- id: {$log->id}",
                "-- message: " . \Str::limit($log->message, 1024, '...'),
                "-- created_at: {$log->created_at}",
            ]));

            $this->newLine();
        }
    }
}
