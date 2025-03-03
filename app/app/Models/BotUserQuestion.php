<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;

class BotUserQuestion extends Model
{
    protected $table = 'bot_user_questions';

    protected $fillable = [
        'bot_user_id',
        'question',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'bot_user_id' => Where::class,
        'question' => Ilike::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'bot_user_id',
        'question',
        'updated_at',
        'created_at',
    ];
}
