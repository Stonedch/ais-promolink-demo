<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
        'show_in_dashboard',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Ilike::class,
        'departament_type_id' => Where::class,
        'sort' => Where::class,
        'rating' => Where::class,
        'inn' => Where::class,
        'dadata' => Ilike::class,
        'address' => Ilike::class,
        'lat' => Where::class,
        'lon' => Where::class,
        'okpo' => Where::class,
        'show_in_dashboard' => Where::class,
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
        'show_in_dashboard',
        'updated_at',
        'created_at',
    ];

    public function getUsers(): Collection
    {
        return User::where('departament_id', $this->id)->get();
    }

    public function customReports(): HasManyThrough
    {
        return $this->hasManyThrough(
            \App\Models\CustomReport::class,
            \App\Models\User::class,
            'departament_id',
            'user_id',
            'id',
            'id'
        );
    }
}
