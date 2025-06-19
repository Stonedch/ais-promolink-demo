<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BotUser;
use App\Models\User;
use Illuminate\Console\Command;
use Throwable;

class BotUserFindCommand extends Command
{
    protected $name = 'bot-user:find';
    protected $signature = 'bot-user:find';
    protected $description = 'bot-user:find';

    public function handle(): void
    {
        BotUser::whereNull('user_id')->get()->map(function (BotUser $botUser) {
            try {
                $user = User::where('phone', $botUser->phone)->first();
                throw_if(empty($user), "User by phone \"{$botUser->phone}\" not found");
                $botUser->user_id = $user->id;
                $botUser->save();
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }
        });
    }
}
