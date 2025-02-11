<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DepartamentType;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormDepartamentType;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReinitEvents extends Command
{
    protected $name = 'app:events:reinit';
    protected $signature = 'app:events:reinit';
    protected $description = 'Создание событий по крону';

    public function handle(): void
    {
        $this->createByPeriodicity(100, now()->today());
        $this->createByPeriodicity(200, now()->subMonth());
    }

    protected function createByPeriodicity(int $type, Carbon $periodicity): void
    {
        Form::query()
            ->where('is_active', true)
            ->where('periodicity', $type)
            ->get()->map(function (Form $form) use ($periodicity) {
                $lastEvent = Event::query()
                    ->where('form_id', $form->id)
                    ->orderBy('created_at', 'desc')
                    ->select(['created_at'])
                    ->first();

                if ($periodicity < $lastEvent->created_at) return;

                DepartamentType::query()
                    ->whereIn('id', FormDepartamentType::where('form_id', $form->id)->pluck('departament_type_id'))
                    ->get()
                    ->map(function (DepartamentType $departamentType) use ($form) {
                        Event::createBy($form, $departamentType);
                    });
            });
    }
}
