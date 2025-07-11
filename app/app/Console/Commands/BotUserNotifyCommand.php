<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BotUserNotificationStatus;
use App\Models\BotUser;
use App\Models\BotUserNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use TelegramBot\Api\BotApi;
use Throwable;

class BotUserNotifyCommand extends Command
{
    protected $name = 'bot-user:notify';
    protected $signature = 'bot-user:notify';
    protected $description = 'bot-user:notify';

    public function handle(): void
    {
        $bot = new BotApi(config('services.telegram.token'));

        BotUserNotification::chunk(100, function (Collection $notifications) use ($bot) {
            $botUsers = BotUser::whereIn('id', $notifications->pluck('bot_user_id'))->get();

            foreach ($notifications as $notification) {
                $status = BotUserNotificationStatus::IN_PROGRESS->value;
                $statusMessage = null;

                try {
                    $data = json_decode($notification->data);
                    $botUser = $botUsers->where('id', $notification->bot_user_id)->first();

                    self::splitMessage($data->message)->map(function (string $part) use ($botUser, $bot) {
                        $bot->sendMessage($botUser->telegram_id, $part);
                    });

                    $status = BotUserNotificationStatus::READY->value;
                    $this->comment('ready');
                } catch (Throwable $e) {
                    $status = BotUserNotificationStatus::ERROR->value;
                    $statusMessage = $e->getMessage();
                    $this->error($e->getMessage());
                } finally {
                    $notification->status = $status;
                    $notification->status_message = $statusMessage;
                    $notification->save();
                    $notification->delete();
                }
            }
        });
    }

    protected static function splitMessage(string $string, int $wordsPerPart = 256): Collection
    {
        $words = explode(' ', $string);
        $parts = [];
        $currentPart = '';

        foreach ($words as $word) {
            $currentPart .= $word . ' ';

            if (count(explode(' ', trim($currentPart))) >= $wordsPerPart) {
                $parts[] = trim($currentPart);
                $currentPart = '';
            }
        }

        if (!empty($currentPart)) {
            $parts[] = trim($currentPart);
        }

        return collect($parts);
    }
}
