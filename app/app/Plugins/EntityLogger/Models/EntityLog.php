<?php

namespace App\Plugins\EntityLogger\Models;

use Illuminate\Database\Eloquent\Model;

class EntityLog extends Model
{
    protected $table = 'entity_logs';

    protected $fillable = [
        'message',
        'model',
        'fields',
        'user',
        'ip',
    ];
}
