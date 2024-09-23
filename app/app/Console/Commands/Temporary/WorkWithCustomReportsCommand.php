<?php

declare(strict_types=1);

namespace App\Console\Commands\Temporary;

use App\Models\CustomReport;
use Illuminate\Console\Command;
use Throwable;

class WorkWithCustomReportsCommand extends Command
{
    protected $name = 'work-custom-reports:run';
    protected $signature = 'work-custom-reports:run';
    protected $description = 'Работа с касмотными отчетами по типам';

    protected const TYPES = [
        'sk' => 2, // Социальные Контракты
        'ack' => 3, // АЦК
        'vsk-users' => 4, // ВСК регистрация пользователей
    ];

    public function handle(): void
    {
        CustomReport::query()
            ->where('worked', false)
            ->leftJoin('attachments', 'attachments.id', '=', 'custom_reports.attachment_id')
            ->select([
                'custom_reports.*',
                'attachments.path as attachment_path',
                'attachments.name as attachment_name',
                'attachments.extension as attachment_extension',
                'attachments.disk as attachment_disk',
            ])
            ->take(5)
            ->get()
            ->map(function (CustomReport $report) {
                $report->worked = true;
                $report->save();

                try {
                    $filepath = storage_path("/app/{$report->attachment_disk}/{$report->attachment_path}{$report->attachment_name}.{$report->attachment_extension}");

                    if ($report->custom_report_type_id == self::TYPES['sk']) {
                        $script = base_path("/temp_21082024/sk.php");
                        $newFilepath = base_path("/temp_21082024/sk.xls");
                        copy($filepath, $newFilepath);
                        shell_exec("php $script") . PHP_EOL;
                    } else if ($report->custom_report_type_id == self::TYPES['ack']) {
                        $script = base_path("/temp_21082024/social_kontract_load_info.php");
                        $newFilepath = base_path("/temp_21082024/АЦК по месяцам 2024.xlsx");
                        copy($filepath, $newFilepath);
                        shell_exec("php $script") . PHP_EOL;
                    } else if ($report->custom_report_type_id == self::TYPES['vsk-users']) {
                        echo shell_exec("sshpass -p \"fkfh77b8\" scp $filepath stonedch@185.246.66.87:/home/stonedch/git/form-filler/app/temp2/soc_contract.xlsx");
                        echo shell_exec("sshpass -p \"fkfh77b8\" ssh stonedch@185.246.66.87 php /home/stonedch/git/form-filler/app/temp2/sk.php");
                    }
                } catch (Throwable $e) {
                    $this->error($e->getMessage());
                }
            });
    }
}
