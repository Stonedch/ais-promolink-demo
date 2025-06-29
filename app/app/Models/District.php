<?php

namespace App\Models;

use App\Plugins\EntityLogger\Observers\EntityLoggerObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

#[ObservedBy([EntityLoggerObserver::class])]
class District extends Model
{
    use AsSource, Filterable;

    protected $table = 'districts';

    protected $fillable = [
        'name',
        'show_minister_view',
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
