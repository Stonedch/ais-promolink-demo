<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class ExternalDepartament extends Model
{
    use AsSource, Filterable;

    protected $table = 'external_departaments';

    protected $fillable = [
        'orgname',
        'orgsokrname',
        'orgpubname',
        'type',
        'post',
        'rukfio',
        'orgfunc',
        'index',
        'region',
        'area',
        'town',
        'street',
        'house',
        'latitude',
        'longitude',
        'mail',
        'telephone',
        'fax',
        'telephonedop',
        'url',
        'okpo',
        'ogrn',
        'inn',
        'schedule',
    ];

    protected $allowedFilters = [
        'id' => Where::class,

        'orgname' => Ilike::class,
        'orgsokrname' => Ilike::class,
        'orgpubname' => Ilike::class,
        'type' => Ilike::class,
        'post' => Ilike::class,
        'rukfio' => Ilike::class,
        'orgfunc' => Ilike::class,
        'index' => Ilike::class,
        'region' => Ilike::class,
        'area' => Ilike::class,
        'town' => Ilike::class,
        'street' => Ilike::class,
        'house' => Ilike::class,
        'latitude' => Ilike::class,
        'longitude' => Ilike::class,
        'mail' => Ilike::class,
        'telephone' => Ilike::class,
        'fax' => Ilike::class,
        'telephonedop' => Ilike::class,
        'url' => Ilike::class,
        'okpo' => Ilike::class,
        'ogrn' => Ilike::class,
        'inn' => Ilike::class,
        'schedule' => Ilike::class,

        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',

        'orgname',
        'orgsokrname',
        'orgpubname',
        'type',
        'post',
        'rukfio',
        'orgfunc',
        'index',
        'region',
        'area',
        'town',
        'street',
        'house',
        'latitude',
        'longitude',
        'mail',
        'telephone',
        'fax',
        'telephonedop',
        'url',
        'okpo',
        'ogrn',
        'inn',
        'schedule',

        'updated_at',
        'created_at',
    ];
}
