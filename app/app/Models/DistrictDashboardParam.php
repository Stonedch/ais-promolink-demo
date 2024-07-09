<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class DistrictDashboardParam extends Model
{
    use AsSource, Filterable;

    protected $table = 'district_dashboard_params';

    protected $fillable = [
        'district_id',
        'name',
        'value',
        'sort',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'district_id' => Where::class,
        'name' => Ilike::class,
        'value' => Ilike::class,
        'sort' => Where::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'district_id',
        'name',
        'value',
        'sort',
        'updated_at',
        'created_at',
    ];
}
