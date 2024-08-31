<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CustomReport;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Orchid\Support\Color;
use PDO;

class CustomReportsCommand extends Command
{
    protected $name = 'custom-reports:run';
    protected $signature = 'custom-reports:run';
    protected $description = 'Работа с касмотными отчетами';

    public function handle(): void
    {
        $connection = self::getConnection();

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
            ->get()
            ->map(function (CustomReport $report) use ($connection) {
                $filepath = "/{$report->attachment_disk}/{$report->attachment_path}{$report->attachment_name}.{$report->attachment_extension}";
                dd($filepath);
            });
    }

    protected static function getConnection(): PDO
    {
        $dbhost = config("database.connections.custom-reports.host");
        $dbname = config("database.connections.custom-reports.database");
        $dbuser = config("database.connections.custom-reports.username");
        $dbpassword = config("database.connections.custom-reports.password");

        return new PDO("pgsql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
    }
}
