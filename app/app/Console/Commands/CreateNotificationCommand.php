<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Console\Command;
use Orchid\Support\Color;

class CreateNotificationCommand extends Command
{
    protected $name = 'noty:create {userIdentifier} {title} {message} {type}';
    protected $signature = 'noty:create {userIdentifier} {title} {message} {type}';
    protected $description = 'Создание уведомления на конкретного пользователя.';

    public function handle(): void
    {
        $title = $this->argument('title');
        $message = $this->argument('message');
        $type = null;
        $strtype = strtolower($this->argument('type'));

        if ($strtype == 'danger') {
            $type = Color::DANGER;
        } elseif ($strtype == 'dark') {
            $type = Color::DARK;
        } else {
            $type = Color::BASIC;
        }

        $notification = new BaseNotification($title, $message, $type);
        User::find($this->argument('userIdentifier'))->notify($notification);
    }
}
