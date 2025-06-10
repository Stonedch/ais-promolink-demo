<?php

namespace App\Models;

use App\Plugins\EntityLogger\Observers\EntityLoggerObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

#[ObservedBy([EntityLoggerObserver::class])]
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
        'parent_id',
        'phone',
        'contact_fullname',
        'email',
        'email_fullname',
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
        'parent_id' => Where::class,
        'phone' => Ilike::class,
        'contact_fullname' => Ilike::class,
        'email' => Ilike::class,
        'email_fullname' => Ilike::class,
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
        'parent_id',
        'phone',
        'contact_fullname',
        'email',
        'email_fullname',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Departament::class, 'parent_id');
    }

    public function getAllSubordinateIds(): Collection
    {
        $ids = [$this->id];
        $this->getChildIdsRecursive($this->id, $ids);
        return collect($ids);
    }


    protected function getChildIdsRecursive(int $parentId, array &$ids): void
    {
        $childIds = Departament::where('parent_id', $parentId)
            ->pluck('id')
            ->toArray();

        if (!empty($childIds)) {
            $ids = array_merge($ids, $childIds);

            foreach ($childIds as $childId) {
                $this->getChildIdsRecursive($childId, $ids);
            }
        }
    }
}
