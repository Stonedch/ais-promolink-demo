<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class FormResult extends Model
{
    use AsSource, Filterable, Attachable;

    protected $table = 'form_results';

    protected $fillable = [
        'user_id',
        'event_id',
        'field_id',
        'value',
        'index',
        'saved_structure',
    ];

    protected $allowedFilters = [
        'id' => Where::class,

        'user_id' => Where::class,
        'event_id' => Where::class,
        'field_id' => Where::class,
        'value' => Like::class,
        'index' => Where::class,

        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',

        'user_id',
        'event_id',
        'field_id',
        'value',
        'index',

        'updated_at',
        'created_at',
    ];
}
