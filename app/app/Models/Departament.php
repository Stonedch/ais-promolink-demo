<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class Departament extends Model
{
    use AsSource, Filterable;

    protected $table = 'departaments';

    protected $fillable = [
        'name',
        'departament_type_id',
        'district_id',
        'sort',
        'rating',
        'inn',
        'dadata',
        'address',
        'lat',
        'lon',
        'okpo',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'departament_type_id' => Where::class,
        'sort' => Where::class,
        'rating' => Where::class,
        'inn' => Where::class,
        'dadata' => Ilike::class,
        'address' => Ilike::class,
        'lat' => Where::class,
        'lon' => Where::class,
        'okpo' => Where::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'departament_type_id',
        'sort',
        'rating',
        'inn',
        'dadata',
        'address',
        'lat',
        'lon',
        'okpo',
        'updated_at',
        'created_at',
    ];

    public function getUsers(): Collection
    {
        return User::where('departament_id', $this->id)->get();
    }
}
