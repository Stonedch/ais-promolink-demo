<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

enum FormCheckerResultStatuses: int
{
    case IN_PROGRESS = 100;
    case ACCEPTED = 200;
    case REJECTED = 300;
}

class FormCheckerResult extends Model
{
    use AsSource, Filterable;

    protected $table = 'form_checker_results';

    protected $fillable = [
        'user_id',
        'form_id',
        'event_id',
        'field_id',
        'status',
    ];

    protected $allowedFilters = [
        'id' => Where::class,

        'user_id' => Where::class,
        'form_id' => Where::class,
        'event_id' => Where::class,
        'field_id' => Where::class,
        'status' => Where::class,

        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',

        'user_id',
        'form_id',
        'event_id',
        'field_id',
        'status',

        'updated_at',
        'created_at',
    ];
}
