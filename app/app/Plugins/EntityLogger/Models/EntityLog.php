<?php

namespace App\Plugins\EntityLogger\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class EntityLog extends Model
{
    use AsSource;

    protected $table = 'entity_logs';

    protected $fillable = [
        'message',
        'model',
        'fields',
        'user',
        'ip',
    ];
}
