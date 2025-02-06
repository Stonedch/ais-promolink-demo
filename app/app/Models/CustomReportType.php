<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class CustomReportType extends Model
{
    use AsSource, Filterable;

    protected $table = 'custom_report_types';

    protected $fillable = [
        'title',
        'is_general',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'title' => Like::class,
        'is_general' => Where::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'title',
        'updated_at',
        'created_at',
    ];

    public function getUsers(): Builder
    {
        $userIdentifiers = CustomReportTypeUser::query()
            ->where('custom_report_type_id', $this->id)
            ->select('user_id');

        return User::query()->whereIn('id', $userIdentifiers);
    }

    public static function byUser(User $user): Collection
    {
        $typeIdentifiers = CustomReportTypeUser::query()
            ->where(function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)->orWhere('is_general', true);
            })
            ->select('custom_report_type_id');

        $customReportTypes = self::whereIn('id', $typeIdentifiers)->get();

        return $customReportTypes;
    }
}
