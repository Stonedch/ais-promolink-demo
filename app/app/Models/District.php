<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class District extends Model
{
    use AsSource, Filterable;

    protected $table = 'districts';

    protected $fillable = [
        'name',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'updated_at',
        'created_at',
    ];

    public function getDashboardParams(): Collection
    {
        return DistrictDashboardParam::where('district_id', $this->id)->get();
    }
}
