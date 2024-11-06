<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class PreparedFormResult extends Model
{
    use AsSource, Filterable;

    public $timestamps = false;

    protected $table = 'prepared_form_results';

    protected $fillable = [
        'prepared_event_id',
        'field_id',
        'row_key_structure',
        'row_key_first',
        'group_key_structure',
        'key',
        'value',
        'index',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'prepared_event_id' => Where::class,
        'field_id' => Where::class,
        'row_key_structure' => Ilike::class,
        'row_key_first' => Ilike::class,
        'group_key_structure' => Ilike::class,
        'key' => Ilike::class,
        'value' => Ilike::class,
        'index' => Ilike::class,
    ];

    protected $allowedSorts = [
        'id',
        'prepared_event_id',
        'field_id',
        'row_key_structure',
        'row_key_first',
        'group_key_structure',
        'key',
        'value',
        'index',
    ];

    public static function deleteByPreapredEvent(PreparedEvent $preparedEvent): void
    {
        self::query()
            ->where('prepared_event_id', $preparedEvent->id)
            ->get()
            ->map(fn(self $preparedFormResult) => $preparedFormResult->delete());
    }
}
