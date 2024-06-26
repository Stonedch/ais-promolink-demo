<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class Form extends Model
{
    use AsSource, Filterable;

    protected $table = 'forms';

    protected $fillable = [
        'name',
        'periodicity',
        'periodicity_step',
        'deadline',
        'type',
        'is_active',
        'is_editable',
        'form_category_id',
    ];

    protected $allowedFilters = [
        'id' => Where::class,

        'name' => Like::class,
        'periodicity' => Where::class,
        'periodicity_step' => Where::class,
        'deadline' => Where::class,
        'type' => Where::class,
        'is_active' => Where::class,
        'is_editable' => Where::class,
        'form_category_id' => Where::class,

        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',

        'name',
        'periodicity',
        'periodicity_step',
        'deadline',
        'type',
        'is_active',
        'is_editable',
        'form_category_id',

        'updated_at',
        'created_at',
    ];

    public static $PERIODICITIES = [
        50 => 'Разовая',
        100 => 'Ежедневно',
    ];

    public static $TYPES = [
        100 => 'Линейный вид',
        200 => 'Табличный вид',
    ];

    public function getStructure()
    {
        return json_encode([
            'form' => $this->toArray(),
            'fields' => Field::where('form_id', $this->id)->get()->toArray(),
        ], JSON_UNESCAPED_UNICODE);
    }
}
