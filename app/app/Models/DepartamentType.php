<?php

namespace App\Models;

use App\Plugins\EntityLogger\Observers\EntityLoggerObserver;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

#[ObservedBy([EntityLoggerObserver::class])]
class DepartamentType extends Model
{
    use AsSource, Filterable;

    protected $table = 'departament_types';

    protected $fillable = [
        'name',
        'show_minister_view',
        'sort',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'sort' => Where::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'sort',
        'updated_at',
        'created_at',
    ];
}
