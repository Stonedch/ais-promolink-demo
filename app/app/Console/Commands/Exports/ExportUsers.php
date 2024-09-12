<?php

declare(strict_types=1);

namespace App\Console\Commands\Exports;

use App\Models\Departament;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Throwable;

class ExportUsers extends Command
{
    protected $name = 'export:users:run';
    protected $signature = 'export:users:run';
    protected $description = 'ExportUsers [export:users:run] - выгрузка пользователей в csv';

    public function handle(): void
    {
        $date = date('d-m-Y-H-i-s');
        $filename = "export-users-{$date}.csv";
        $filepath = storage_path("app/private/exports/$filename");

        $file = fopen($filepath, 'a+');

        try {
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fwrite($file, implode(';', [
                'Номер телефона',
                'E-mail',
                'Фамилия',
                'Имя',
                'Отчество',
                'Учреждение',
                'Роль',
            ]) . PHP_EOL);

            User::chunk(500, function (Collection $users) use ($file){
                $departaments = Departament::whereIn('id', $users->pluck('departament_id'))->get();

                foreach ($users as $user) {
                    $row = [
                        $user->phone,
                        $user->email,
                        $user->last_name,
                        $user->first_name,
                        $user->middle_name,
                        $user->departament_id ? $departaments->where('id', $user->departament_id)->first()->name : null,
                        implode(', ', $user->getRoles()->pluck('name')->toArray())
                    ];

                    fwrite($file, implode(';', $row) . PHP_EOL);
                }
            });
        } catch (Throwable $e) {
            echo "Ошибка: {$e->getMessage()}\n";
        } finally {
            echo "Путь к файлу: $filepath\n";
            fclose($file);
        }
    }
}
