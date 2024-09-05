<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class BotUserNotification extends Model
{
    use AsSource, Filterable, SoftDeletes;

    protected $table = 'bot_user_notifications';

    protected $fillable = [
        'bot_user_id',
        'data',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'bot_user_id' => Where::class,
        'data' => Ilike::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
        'deleted_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'bot_user_id',
        'data',
        'telegram_id',
        'updated_at',
        'created_at',
        'deleted_at',
    ];
}
