<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class DepartamentType extends Model
{
    use AsSource, Filterable;

    protected $table = 'departament_types';

    protected $fillable = [
        'name',
        'show_minister_view',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'updated_at',
        'created_at',
    ];
}
