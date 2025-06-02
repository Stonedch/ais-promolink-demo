<?php

namespace App\Models;

use App\Plugins\EntityLogger\Observers\EntityLoggerObserver;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

#[ObservedBy([EntityLoggerObserver::class])]
class FormFieldBlocked extends Model
{
    use AsSource, Filterable;

    protected $table = 'form_field_blockeds';

    protected $fillable = [
        'value',
        'form_id',
        'field_id',
        'index',
    ];

    protected $allowedFilters = [
        'id' => Where::class,

        'value' => Ilike::class,
        'form_id' => Where::class,
        'field_id' => Where::class,
        'index' => Where::class,

        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',

        'value',
        'form_id',
        'field_id',
        'index',

        'updated_at',
        'created_at',
    ];
}
