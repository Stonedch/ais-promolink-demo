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
class Field extends Model
{
    use AsSource, Filterable;

    protected $fillable = [
        'form_id',
        'name',
        'group',
        'group_id',
        'type',
        'sort',
        'collection_id',
        'checker_user_id',
    ];

    protected $allowedFilters = [
        'id' => Where::class,

        'form_id' => Where::class,
        'name' => Like::class,
        'group' => Like::class,
        'type' => Where::class,
        'sort' => Where::class,
        'collection_id' => Where::class,
        'checker_user_id' => Where::class,

        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'updated_at',
        'created_at',
    ];

    public static $TYPES = [
        100 => 'Текстовое поле',
        200 => 'Одиночный выбор',
        300 => 'Множественный выбор',
        400 => 'Дата',
        500 => 'Число',
        700 => 'Множественная загрузка файлов',
    ];
}
