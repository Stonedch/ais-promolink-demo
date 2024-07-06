<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class FormCategory extends Model
{
    use AsSource, Filterable;

    protected $table = 'form_categories';

    protected $fillable = [
        'name',
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
