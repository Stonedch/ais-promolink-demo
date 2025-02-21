<?php

namespace App\Http\Controllers\Api;

use App\Helpers\BotHelpers\TelegramBotHelper;
use App\Helpers\PhoneNormalizer;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Response;
use TelegramBot\Api\Client;
use Throwable;

class TelegramController extends Controller
{
    public function handle(): Response
    {
        $bot = new Client(self::getToken());

        $bot->on(function (\TelegramBot\Api\Types\Update $update) use ($bot) {
            $message = $update->getMessage();
            if (empty($message)) return;
            $id = $message->getChat()->getId();

            if (!is_null(TelegramBotHelper::getUserByUserId($id))) {
                $bot->sendMessage($message->getChat()->getId(), 'Вы уже зарегистрировались: ожидайте уведомлений');
            } elseif ($message->getContact() != null) {
                try {
                    $phone = PhoneNormalizer::normalizePhone($message->getContact()->getPhoneNumber());
                    TelegramBotHelper::store($phone, $id);
                    $bot->sendMessage($message->getChat()->getId(), 'Вы успешно зарегистрировались: уведомления будут дублироваться в этот чат-бот');
                } catch (Trowable $e) {
                    $bot->sendMessage($message->getChat()->getId(), 'Пожалуйста, предоставьте Ваш номер телефона в телеграм!');
                }
            } else {
                $buttons = [[
                    ['text' => 'Отправить номер', 'request_contact' => true]
                ]];

                $replyMarkup = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($buttons, true, false, true);

                $bot->sendMessage($message->getChat()->getId(), "Для получения инфомрации необходимо пройти процедуру регистрации, предоставив свой номер телефона. \r\n Нажмите ниже на кнопку \"Отправить номер\"", null, false, null, $replyMarkup);
            }
        }, function () {
            return true;
        });

        $bot->run();

        return response()->noContent();
    }

    private static function getToken(): string
    {
        return config('services.telegram.token');
    }
}
