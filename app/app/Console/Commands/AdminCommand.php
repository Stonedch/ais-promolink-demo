<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Orchid\Platform\Commands\AdminCommand as CommandsAdminCommand;
use Orchid\Support\Facades\Dashboard;

class AdminCommand extends CommandsAdminCommand
{

    /**
     * @var string
     */
    protected $signature = 'orchid:admin {phone?} {password?} {--id=}';

    protected function createNewUser(): void
    {
        Dashboard::modelClass(User::class)
            ->createAdminByPhone(
                $this->argument('phone') ?? $this->ask('What is your phone?', '+7 (999) 999-99-99'),
                $this->argument('password') ?? $this->secret('What is the password?')
            );

        $this->info('User created successfully.');
    }
}
