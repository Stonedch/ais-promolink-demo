<?php

namespace App\Models;

use App\Plugins\EntityLogger\Observers\EntityLoggerObserver;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

#[ObservedBy([EntityLoggerObserver::class])]
class CustomReportTypeUser extends Model
{
    use AsSource, Filterable;

    protected $table = 'custom_report_types_users';

    protected $fillable = [
        'user_id',
        'custom_report_type_id',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'user_id' => Where::class,
        'custom_report_type_id' => Where::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'user_id',
        'custom_report_type_id',
        'updated_at',
        'created_at',
    ];
}
