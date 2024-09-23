<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BotUser;
use App\Models\BotUserNotification;
use App\Models\CustomReport;
use App\Models\Event;
use App\Models\Field;
use App\Models\FormResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DebugCommand extends Command
{
    protected $name = 'debug:run';
    protected $signature = 'debug:run';
    protected $description = 'This\'s just debug command.';

    // Please clear me after debug
    public function handle(): void
    {
        $botUser = BotUser::where('user_id', 1)->first();
        (new BotUserNotification())->fill([
            'bot_user_id' => $botUser->id,
            'data' => '{"time":"2024-09-05T06:54:01.510934Z","type":"default","title":"Уведомление!","message":"Тело сообщения!"}'
        ])->save();
        // dd(BotUserNotification::where('bot_user_id', $botUser->id)->first());
    }
}
