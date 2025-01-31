<?php

namespace App\Models;

use App\Enums\EventStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class Event extends Model
{
    use AsSource, Filterable;

    protected $table = 'events';

    protected $fillable = [
        'form_id',
        'departament_id',
        'form_structure',
        'filled_at',
        'refilled_at',
        'saved_structure',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'form_id' => Where::class,
        'departament_id' => Where::class,
        'form_structure' => Like::class,
        'filled_at' => WhereDateStartEnd::class,
        'refilled_at' => WhereDateStartEnd::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'form_id',
        'departament_id',
        'form_structure',
        'filled_at',
        'refilled_at',
        'updated_at',
        'created_at',
    ];

    public function getCurrentStatus()
    {
        $formStructure = is_array($this->form_structure)
            ? $this->form_structure
            : json_decode($this->form_structure, true);
        $deadline = $formStructure['form']['deadline'];
        $diff = now()->diffInSeconds((new Carbon($this->created_at))->addDays($deadline));
        $isFilled = empty($this->filled_at) == false;

        if ($isFilled) {
            return EventStatus::from(100);
        } elseif (empty($deadline) == false && $diff < 0) {
            return EventStatus::from(200);
        } else {
            return EventStatus::from(300);
        }
    }

    // TODO: rename me pls
    public static function createBy(Form $form, DepartamentType $departamentType)
    {
        $formStructure = $form->getStructure();

        Departament::query()
            ->where('departament_type_id', $departamentType->id)
            ->get()
            ->map(function (Departament $departament) use ($formStructure, $form) {
                self::createByDistrict($form, $departament, $formStructure);
            });
    }

    public static function createByDistrict(Form $form, Departament $departament, string $formStructure = null)
    {
        (new Event())->fill([
            'form_id' => $form->id,
            'form_structure' => $formStructure ?: $form->getStructure(),
            'departament_id' => $departament->id,
        ])->save();
    }

    public function formResults(): HasMany
    {
        return $this->hasMany(FormResult::class);
    }

    public function getStructure(object $structure = null): object
    {
        return empty($structure) == false ? $structure : json_decode($this->form_structure);
    }

    public function getStructureFields(object $structure = null): Collection
    {
        $structure = $this->getStructure($structure);
        return collect($structure->fields)->keyBy('id');
    }

    public function getStructureGroups(object $structure = null): Collection
    {
        $structure = $this->getStructure($structure);

        return isset($structure->groups)
            ? collect($structure->groups)->keyBy('id')
            : new Collection();
    }

    public function getStructureBlockeds(object $structure = null): Collection
    {
        $structure = $this->getStructure($structure);

        return isset($structure->blockeds)
            ? collect($structure->blockeds)->keyBy('id')
            : new Collection();
    }

    public static function lastByDepartament(int $formIdentifier, int $departamentIdentifier): ?self
    {
        return self::query()
            ->where('form_id', $formIdentifier)
            ->where('departament_id', $departamentIdentifier)
            ->orderBy('id', 'DESC')
            ->first();
    }
}
