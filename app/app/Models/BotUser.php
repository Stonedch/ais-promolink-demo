<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;

class BotUser extends Model
{
    protected $table = 'bot_users';

    protected $fillable = [
        'user_id',
        'phone',
        'telegram_id',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'user_id' => Where::class,
        'phone' => Ilike::class,
        'telegram_id' => Where::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'user_id',
        'phone',
        'telegram_id',
        'updated_at',
        'created_at',
    ];
}
