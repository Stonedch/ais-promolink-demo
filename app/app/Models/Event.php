<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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

    public static $STATUSES = [
        100 => 'В процессе',
        200 => 'Просрочен',
        300 => 'Выполнен',
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
            return 300;
        } elseif ($diff < 0) {
            return 200;
        } else {
            return 100;
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
}
