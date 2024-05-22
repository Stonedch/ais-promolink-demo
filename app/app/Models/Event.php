<?php

namespace App\Models;

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

    public static function createBy(Form $form, DepartamentType $departamentType)
    {
        $formStructure = $form->getStructure();

        Departament::query()
            ->where('departament_type_id', $departamentType->id)
            ->get()
            ->map(function (Departament $departament) use ($formStructure, $form) {
                $event = new Event();

                $event->fill([
                    'form_id' => $form->id,
                    'departament_id' => $departament->id,
                    'form_structure' => $formStructure,
                ]);

                $event->save();
            });
    }
}
