<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ExternalDepartament;
use Illuminate\Console\Command;
use Throwable;

class ImportExternalDepartaments extends Command
{
    protected $name = 'import-external-departaments:run';
    protected $signature = 'import-external-departaments:run';

    public function handle(): void
    {
        $filepath = storage_path() . $this->argument('filepath');
        $file = fopen($filepath, 'r');

        ExternalDepartament::all()->map(function (ExternalDepartament $externalDepartament) {
            $externalDepartament->delete();
        });

        try {
            $index = 0;
            while ($line = fgetcsv($file, null, ';')) {
                if ($index++ == 0) continue;

                try {
                    (new ExternalDepartament())->fill([
                        'orgname' => @$line[0] ?: null,
                        'orgsokrname' => @$line[1] ?: null,
                        'orgpubname' => @$line[2] ?: null,
                        'type' => @$line[3] ?: null,
                        'post' => @$line[4] ?: null,
                        'rukfio' => @$line[5] ?: null,
                        'orgfunc' => @$line[6] ?: null,
                        'index' => @$line[7] ?: null,
                        'region' => @$line[8] ?: null,
                        'area' => @$line[9] ?: null,
                        'town' => @$line[10] ?: null,
                        'street' => @$line[11] ?: null,
                        'house' => @$line[12] ?: null,
                        'latitude' => @$line[13] ?: null,
                        'longitude' => @$line[14] ?: null,
                        'mail' => @$line[15] ?: null,
                        'telephone' => @$line[16] ?: null,
                        'fax' => @$line[17] ?: null,
                        'telephonedop' => @$line[18] ?: null,
                        'url' => @$line[19] ?: null,
                        'okpo' => @$line[20] ?: null,
                        'ogrn' => @$line[21] ?: null,
                        'inn' => @$line[22] ?: null,
                        'schedule' => @$line[23] ?: null,
                    ])->save();
                } catch (Throwable) {
                    echo " - [$index] error!" . PHP_EOL;
                    continue;
                }

                echo " - [$index] ready" . PHP_EOL;
            }
        } catch (Throwable $e) {
            echo ' - ' . $e->getMessage() . PHP_EOL;
        } finally {
            fclose($file);
        }
    }
}
