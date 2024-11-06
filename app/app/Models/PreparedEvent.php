<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class PreparedEvent extends Model
{
    use AsSource, Filterable;

    public $timestamps = false;

    protected $table = 'prepared_events';

    protected $fillable = [
        'event_id',
        'user_fullname',
        'departament_name',
        'form_name',
        'event_created_at',
        'event_filled_at',
        'event_refilled_at',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'event_id' => Where::class,
        'user_fullname' => Ilike::class,
        'departament_name' => Ilike::class,
        'form_name' => Ilike::class,
        'event_created_at' => WhereDateStartEnd::class,
        'event_filled_at' => WhereDateStartEnd::class,
        'event_refilled_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'event_id',
        'user_fullname',
        'departament_name',
        'form_name',
        'event_created_at',
        'event_filled_at',
        'event_refilled_at',
    ];

    public static function findByEventOrCreate(Event $event): PreparedEvent
    {
        return PreparedEvent::where('event_id', $event->id)->first() ?: new PreparedEvent();
    }
}
