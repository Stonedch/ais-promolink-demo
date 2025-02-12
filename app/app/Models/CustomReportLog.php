<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class CustomReportLog extends Model
{
    use AsSource, Filterable;

    protected $table = 'custom_report_logs';

    protected $fillable = [
        'type',
        'message',
        'custom_report_type_id',
        'custom_report_id',
        'user_id',
        'filepath',
        'template_filepath',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'type' => Where::class,
        'message' => Ilike::class,
        'custom_report_type_id' => Where::class,
        'custom_report_id' => Where::class,
        'user_id' => Where::class,
        'filepath' => Ilike::class,
        'template_filepath' => Ilike::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'type',
        'message',
        'custom_report_type_id',
        'custom_report_id',
        'user_id',
        'filepath',
        'template_filepath',
        'updated_at',
        'created_at',
    ];
}
