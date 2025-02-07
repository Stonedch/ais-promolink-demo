<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
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
        'attachment_id',
        'is_freelance',
        'command',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'title' => Ilike::class,
        'is_general' => Where::class,
        'attachment_id' => Where::class,
        'is_freelance' => Where::class,
        'command' => Ilike::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'title',
        'is_general',
        'attachment_id',
        'is_freelance',
        'command',
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
            ->where('user_id', $user->id)
            ->select('custom_report_type_id');

        $customReportTypes = self::query()
            ->whereIn('id', $typeIdentifiers)
            ->orWhere('is_general', true)
            ->get();

        return $customReportTypes;
    }
}
