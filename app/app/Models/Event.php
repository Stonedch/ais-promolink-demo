<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Notifications\BaseNotification;
use App\Plugins\EntityLogger\Observers\EntityLoggerObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;
use Orchid\Support\Color;

#[ObservedBy([EntityLoggerObserver::class])]
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
        'changing_filled_at',
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
        'changing_filled_at' => WhereDateStartEnd::class,
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
        'changing_filled_at',
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
            return EventStatus::from(300);
        } elseif (empty($deadline) == false && $diff < 0) {
            return EventStatus::from(200);
        } else {
            return EventStatus::from(100);
        }
    }

    // TODO: rename me pls
    public static function createBy(Form $form, DepartamentType $departamentType): Collection
    {
        $formStructure = $form->getStructure();

        $events = [];

        Departament::query()
            ->where('departament_type_id', $departamentType->id)
            ->get()
            ->map(function (Departament $departament) use ($formStructure, $form, &$events) {
                $events[] = self::createByDistrict($form, $departament, $formStructure);
            });

        return collect($events);
    }

    public static function createByDistrict(Form $form, Departament $departament, string $formStructure = null): Event
    {
        $event = new Event();

        $event->fill([
            'form_id' => $form->id,
            'form_structure' => $formStructure ?: $form->getStructure(),
            'departament_id' => $departament->id,
        ])->save();

        User::where('departament_id', $departament->id)->get()->map(function (User $user) use ($form) {
            $notification = new BaseNotification(
                'Новый отчет',
                "Добавлен новый отчет на заполнение \"{$form->name}\"",
                Color::BASIC
            );

            $user->notify($notification);

            return $user;
        });

        return $event;
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
            ->orderBy('id', 'DESC')
            ->where('form_id', $formIdentifier)
            ->where('departament_id', $departamentIdentifier)
            ->first();
    }
}
