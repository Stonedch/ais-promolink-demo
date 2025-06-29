<?php

namespace App\Models;

use App\Plugins\EntityLogger\Observers\EntityLoggerObserver;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

#[ObservedBy([EntityLoggerObserver::class])]
class FormGroup extends Model
{
    use AsSource, Filterable;

    protected $table = 'form_groups';

    protected $fillable = [
        'name',
        'slug',
        'sort',
        'form_id',
        'parent_id',
        'is_multiple',
    ];

    protected $allowedFilters = [
        'id' => Where::class,

        'name' => Ilike::class,
        'slug' => ILike::class,
        'sort' => Where::class,
        'form_id' => Where::class,
        'parent_id' => Where::class,
        'is_multiple' => Where::class,

        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',

        'name',
        'slug',
        'sort',
        'form_id',
        'parent_id',
        'is_multiple',

        'updated_at',
        'created_at',
    ];
}
