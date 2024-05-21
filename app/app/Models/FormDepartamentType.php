<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class FormDepartamentType extends Model
{
    use AsSource, Filterable;

    protected $table = 'form_departament_types';

    protected $fillable = [
        'form_id',
        'departament_type_id',
    ];

    protected $allowedFilters = [
        'id' => Where::class,

        'form_id' => Where::class,
        'departament_type_id' => Where::class,

        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',

        'form_id',
        'departament_type_id',

        'updated_at',
        'created_at',
    ];
}
