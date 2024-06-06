<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DepartamentType;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormDepartamentType;
use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Query\Builder;

class ReinitEvents extends Command
{
    protected $name = 'app:events:reinit';
    protected $signature = 'app:events:reinit';
    protected $description = 'Создание событий по крону';

    public function handle(): void
    {
        Form::query()
            ->leftJoin('events', 'events.form_id', 'forms.id')
            ->where('is_active', true)
            ->where('periodicity', '100')
            ->where(function (Builder $query) {
                $query->where('events', null)
                    ->orWhere('events.created_at', '<', now()->today());
            })
            ->select('forms.*')
            ->get()
            ->map(function (Form $form) {
                DepartamentType::query()
                    ->whereIn('id', FormDepartamentType::where('form_id', $form->id)->pluck('departament_type_id'))
                    ->get()
                    ->map(function (DepartamentType $departamentType) use ($form) {
                        Event::createBy($form, $departamentType);
                    });
            });
    }
}
