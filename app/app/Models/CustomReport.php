<?php

namespace App\Models;

use App\Enums\CustomReportLogType;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
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

    public function getWorkedErrorMessage(string $separator = '; ')
    {
        try {
            throw_if($this->getWorkedStatus() == 'Выполнен');

            $logs = CustomReportLog::query()
                ->where('custom_report_id', $this->id)
                ->where('type', CustomReportLogType::ERROR_MESSAGE->value)
                ->pluck('message');

            throw_if(empty($logs->count()));

            return implode($separator, $logs->toArray());
        } catch (Throwable) {
            return '-';
        }
    }
}
