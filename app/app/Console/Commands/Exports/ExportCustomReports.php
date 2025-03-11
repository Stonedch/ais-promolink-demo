<?php

declare(strict_types=1);

namespace App\Console\Commands\Exports;

use App\Collections\AttachmentCollection;
use App\Models\CustomReport;
use App\Models\Departament;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Throwable;

class ExportCustomReports extends Command
{
    protected $name = 'export:custom-reports:run {departament}';
    protected $signature = 'export:custom-reports:run {departament}';
    protected $description = 'ExportCustomReports [export:custom-reports:run] - выгрузка файлов кастомных отчетов в архив по';

    public function handle(): void
    {
        $departament = Departament::find($this->argument('departament'));
        $attachmentIdentifiers = CustomReport::query()
            ->whereIn('user_id', User::where('departament_id', $departament->id)->select('id'))
            ->pluck('attachment_id');
        $attachments = new AttachmentCollection($attachmentIdentifiers);
        $filepath = $attachments->zip($departament->name . '-' . now()->getTimestamp());
        $this->comment($filepath);
    }
}
