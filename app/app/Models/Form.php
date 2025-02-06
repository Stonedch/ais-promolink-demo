<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;
use Throwable;

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
        'sort',
        'by_initiative',
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
        'by_initiative' => Where::class,

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
        'by_initiative',

        'updated_at',
        'created_at',
    ];

    public static $PERIODICITIES = [
        50 => 'Разовая',
        100 => 'Ежедневная',
        200 => 'Ежемесячная',
    ];

    public static $TYPES = [
        100 => 'Линейный вид',
        200 => 'Табличный вид',
        300 => 'Сводный вид',
    ];

    public function getStructure()
    {
        $form = $this;
        $fields = Field::where('form_id', $this->id)->get();
        $groups = FormGroup::where('form_id', $this->id)->get();
        $blockeds = FormFieldBlocked::where('form_id', $this->id)->get();
        $collections = Collection::whereIn('id', $fields->pluck('collection_id'))->get();
        $collectionValues = CollectionValue::whereIn('collection_id', $collections->pluck('id'))->get();

        return json_encode([
            'form' => $form->toArray(),
            'fields' => $fields->toArray(),
            'groups' => $groups->toArray(),
            'blockeds' => $blockeds->toArray(),
            'collections' => $collections->toArray(),
            'collectionValues' => $collectionValues->toArray(),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function departamentTypes(): BelongsToMany
    {
        return $this->belongsToMany(DepartamentType::class, 'form_departament_types');
    }

    public function canUserEdit(): bool
    {
        try {
            throw_if(empty(@$this->last_event));
            throw_if(empty(@$this->last_event->filled_at) == false);
            throw_if(request()->user()->hasAnyAccess(['platform.supervisor.base']));
            throw_if(request()->user()->hasAnyAccess(['platform.departament-director.base']));
            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
