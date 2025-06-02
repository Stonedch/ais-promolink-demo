<?php

namespace App\Plugins\IBKnowledgeBase\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class InformationSystem extends Model
{
    use AsSource, Filterable;

    protected $table = 'ibkb_information_systems';

    protected $fillable = [
        'name',
        'description',
    ];
}