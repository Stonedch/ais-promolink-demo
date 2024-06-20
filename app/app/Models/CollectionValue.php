<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;

class CollectionValue extends Model
{
    protected $table = 'collection_values';
    protected $fillable = [
        'value',
        'collection_id',
        'sort',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'value' => Like::class,
        'sort' => Where::class,
        'collection_id' => Where::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'value',
        'sort',
        'collection_id',
        'updated_at',
        'created_at',
    ];
}
