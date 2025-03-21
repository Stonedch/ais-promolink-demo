<?php

namespace App\Models;

use App\Enums\CustomReportLogType;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;
use Throwable;

class CustomReport extends Model
{
    use AsSource, Filterable;

    protected $table = 'custom_reports';

    protected $fillable = [
        'user_id',
        'custom_report_type_id',
        'attachment_id',
        'worked',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'user_id' => Where::class,
        'custom_report_type_id' => Where::class,
        'attachment_id' => Where::class,
        'worked' => Where::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'title',
        'user_id',
        'custom_report_type_id',
        'attachment_id',
        'worked',
        'updated_at',
        'created_at',
    ];

    protected const LOG_ERROR_MESSAGES = [
        'loaded' => 'Документ загружен',
        'is ready' => 'Документь полностью обработан',
        'SQLSTATE' => 'Внутренняя ошибка, обратитесь в тех. поддержку',
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function (CustomReport $customReport) {
            dispatch(fn() => Artisan::call('custom-reports:run'));
        });
    }

    public function getWorkedStatus()
    {
        $status = 'В работе';

        if ($this->worked) {
            $status = 'Выполнен';
        } elseif ($this->worked == false and empty($this->worked_at) == false) {
            $status = 'Ошибка загрузки';
        }

        return $status;
    }

    public function getWorkedErrorMessage(string $separator = '; ', $replacing = false)
    {
        try {
            throw_if($this->getWorkedStatus() == 'Выполнен');

            $query = CustomReportLog::query()
                ->where('custom_report_id', $this->id)
                ->where('type', CustomReportLogType::ERROR_MESSAGE->value);

            $count = $query->count();

            $logs = Cache::remember(
                "CustomReport.getWorkedErrorMessage.v0.[{$this->id};{$count}]",
                now()->addDays(7),
                function () use ($query, $replacing) {
                    $logs = $query->pluck('message');

                    if ($replacing) {
                        foreach ($logs as $i => $message) {
                            foreach (self::LOG_ERROR_MESSAGES as $key => $replacement) {
                                if (str_contains($message, $key)) {
                                    $logs[$i] = $replacement;
                                }
                            }
                        }
                    }

                    return $logs;
                }
            );

            throw_if(empty($logs->count()));

            return implode($separator, $logs->toArray());
        } catch (Throwable) {
            return '-';
        }
    }
}
