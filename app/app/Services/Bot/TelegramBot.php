<?php

namespace App\Services\Bot;

use App\Models\BotUser;
use App\Models\BotUserNotification;
use App\Models\User;
use App\Notifications\DashboardMessageNotification;
use App\Services\Normalizers\PhoneNormalizer;
use Exception;
use Illuminate\Support\Collection;
use Orchid\Platform\Notifications\DashboardMessage;
use Orchid\Support\Color;

class TelegramBot
{
    public static function getUser(string $phone): ?BotUser
    {
        $phone = PhoneNormalizer::normalizePhone($phone);
        $botUser = BotUser::where('phone', $phone)->first();
        return $botUser;
    }

    public static function getUserByUserId(string $userId): ?BotUser
    {
        $botUser = BotUser::where('telegram_id', $userId)->first();
        return $botUser;
    }

    public static function store(string $phone, string $telegramId): BotUser
    {
        $phone = PhoneNormalizer::normalizePhone($phone);
        $botUser = self::getUser($phone) ?: new BotUser();

        $botUser->fill([
            'phone' => $phone,
            'telegram_id' => $telegramId,
        ])->save();

        return $botUser;
    }

    public static function notify(
        User $user,
        string $title,
        string $body,
        Color $type = Color::BASIC,
        bool $withDatabaseNotification = true,
    ): BotUserNotification {
        $botUser = BotUser::where('phone', $user->phone)->first();

        throw_if(empty($botUser), new Exception("BotUser with phone \"{$user->phone}\" undefined"));

        $notification = (new DashboardMessage())
            ->title($title)
            ->message($body)
            ->type($type);

        if ($withDatabaseNotification) {
            $user->notify(new DashboardMessageNotification($notification));
        }

        $botUserNotification = new BotUserNotification();

        $botUserNotification->fill([
            'bot_user_id' => $botUser->id,
            'data' => json_encode($notification->data, JSON_UNESCAPED_UNICODE),
        ])->save();

        return $botUserNotification;
    }

    public static function notifyBot(
        BotUser $botUser,
        string $title,
        string $body
    ): BotUserNotification {
        $notification = (new DashboardMessage())
            ->title($title)
            ->message($body)
            ->type(Color::BASIC);

        $botUserNotification = new BotUserNotification();

        $botUserNotification->fill([
            'bot_user_id' => $botUser->id,
            'data' => json_encode($notification->data, JSON_UNESCAPED_UNICODE),
        ])->save();

        return $botUserNotification;
    }

    public static function pop(): Collection
    {
        $notifications = BotUserNotification::all();
        self::deleteNotifications($notifications);
        return $notifications;
    }

    public static function popByPhone(string $phone): Collection
    {
        $notifications = BotUserNotification::where('phone', $phone)->get();
        self::deleteNotifications($notifications);
        return $notifications;
    }

    public static function popByTelegramId(string $telegramId): Collection
    {
        $notifications = BotUserNotification::where('telegram_id', $telegramId)->get();
        self::deleteNotifications($notifications);
        return $notifications;
    }

    private static function deleteNotifications(Collection $notifications)
    {
        $notifications->map(fn(BotUserNotification $notification) => $notification->delete());
    }
}
