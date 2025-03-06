<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;
use Throwable;

class BotUserQuestion extends Model
{
    use AsSource, Filterable, SoftDeletes;

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

    public function getUser(): ?User
    {
        try {
            return User::find(BotUser::find($this->bot_user_id)->user_id);
        } catch (Throwable) {
            return null;
        }
    }

    public function getUserIdentifier(): ?int
    {
        try {
            return BotUser::find($this->bot_user_id)->user_id;
        } catch (Throwable) {
            return null;
        }
    }
}
