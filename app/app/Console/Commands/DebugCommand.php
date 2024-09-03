<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\PhoneNormalizer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Orchid\Platform\Models\Role;
use Throwable;

class DebugCommand extends Command
{
    protected $name = 'debug:run';
    protected $signature = 'debug:run';
    protected $description = 'This\'s just debug command.';

    // Please clear me after debug
    public function handle(): void
    {
        $filepath = storage_path('/app/needed-users.csv');
        $file = fopen($filepath, 'r');

        $usersFilepath = storage_path('/app/users.csv');
        $usersFile = fopen($usersFilepath, 'a+');
        fputs($usersFile, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $roles = Role::where('slug', 'ILIKE', 'user')->pluck('id')->toArray();

        try {
            $index = 0;

            fputcsv($usersFile, [
                'Фамилия',
                'Имя',
                'Отчество',
                'Номер телефона',
                'Пароль',
                'E-mail',
            ], ';');

            while ($line = fgetcsv($file, null, '	')) {
                if (empty($line)) break;
                if ($index++ <= 0) continue;

                $phone = PhoneNormalizer::normalizePhone($line[5]);
                $password = $line[6] ?: self::genPassword();

                if (empty($phone)) continue;
                
                $userData = [
                    'last_name' => $line[2],
                    'first_name' => $line[3],
                    'middle_name' => $line[4],
                    'phone' => $phone,
                    'password' => Hash::make($password),
                ];

                $user = User::where('phone', $phone)->first() ?: new User();
                $user->fill($userData);
                $user->save();

                $user->replaceRoles($roles);

                $userData['password'] = $password;

                fputcsv($usersFile, $userData, ';');
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        } finally {
            fclose($file);
            fclose($usersFilepath);
        }
    }

    private static function genPassword(): string
    {
        return str_replace(';', '%', \Illuminate\Support\Str::password(8));
    }

    private static function genEmail(string $phone): string
    {
        return "{$phone}@promo-link.ru";
    }
}
