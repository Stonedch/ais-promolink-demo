<?php

namespace App\Models;

use App\Plugins\EntityLogger\Observers\EntityLoggerObserver;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

#[ObservedBy([EntityLoggerObserver::class])]
class FormChecker extends Model
{
    use AsSource, Filterable;

    protected $table = 'form_checker';

    protected $fillable = [
        'user_id',
        'field_id',
        'form_id',
    ];

    protected $allowedFilters = [
        'id' => Where::class,

        'user_id' => Where::class,
        'field_id' => Where::class,
        'form_id' => Where::class,

        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',

        'user_id',
        'field_id',
        'form_id',

        'updated_at',
        'created_at',
    ];
}
