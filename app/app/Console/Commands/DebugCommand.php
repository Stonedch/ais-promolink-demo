<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\FieldGetter;
use App\Models\Departament;
use App\Models\FormCheckerResult;
use Illuminate\Console\Command;

class DebugCommand extends Command
{
    protected $name = 'debug:run';
    protected $signature = 'debug:run';
    protected $description = 'This\'s just debug command.';

    // Please clear me after debug
    public function handle(): void
    {
        FormCheckerResult::all()->map(function (FormCheckerResult $result) {
            $result->delete();
        });
        // $departament = Departament::find(210);

        // FieldGetter::find(
        //     $departament,
        //     [69, 67, 64, 63, 46, 44, 40, 33, 29, 25],
        //     ['Число работников', 'Техническое обеспечение']
        // );
    }
}
