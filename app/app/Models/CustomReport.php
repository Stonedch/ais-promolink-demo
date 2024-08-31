<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

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
}
