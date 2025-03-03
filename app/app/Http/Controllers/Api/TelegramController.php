<?php

namespace App\Http\Controllers\Api;

use App\Enums\BotUserEvent;
use App\Helpers\BotHelpers\TelegramBotHelper;
use App\Helpers\PhoneNormalizer;
use App\Http\Controllers\Controller;
use App\Models\BotUserQuestion;
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


            try {
                $botUser = TelegramBotHelper::getUserByUserId($id);
                $user_start_question = $botUser->event == BotUserEvent::ADD_QUERY->value;
                if ($user_start_question === true) {
                    // сохранить вопрос
                    $buttons = [[
                        ['text' => 'Задать вопрос']
                    ]];

                    (new BotUserQuestion())->fill([
                        'bot_user_id' => $botUser->id,
                        'question' => $message->getText(),
                    ])->save();

                    $botUser = TelegramBotHelper::getUserByUserId($id);
                    $botUser->event = null;
                    $botUser->save();

                    $replyMarkup = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($buttons, true, false, true);
                    $bot->sendMessage($message->getChat()->getId(), 'Ваш вопрос сохранен: пожалуйста, ожидайте ответа', null, false, null, $replyMarkup);
                    return;
                }
            } catch (Throwable) {
            }

            if ($message->getText() == "Задать вопрос") {
                // надо сделать пометку, что от пользователя ожидается вопрос
                $bot->sendMessage($message->getChat()->getId(), 'Направьте текст Вашего вопроса одним сообщением');
                $botUser = TelegramBotHelper::getUserByUserId($id);
                $botUser->event = BotUserEvent::ADD_QUERY->value;
                $botUser->save();
            } elseif (!is_null(TelegramBotHelper::getUserByUserId($id))) {
                $buttons = [[
                    ['text' => 'Задать вопрос']
                ]];
                $replyMarkup = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($buttons, true, false, true);
                $bot->sendMessage($message->getChat()->getId(), 'Вы уже зарегистрировались: ожидайте уведомлений', null, false, null, $replyMarkup);
            } elseif ($message->getContact() != null) {
                try {
                    $phone = PhoneNormalizer::normalizePhone($message->getContact()->getPhoneNumber());
                    TelegramBotHelper::store($phone, $id);
                    // $bot->sendMessage($message->getChat()->getId(), 'Вы успешно зарегистрировались: уведомления будут дублироваться в этот чат-бот');

                    $buttons = [[
                        ['text' => 'Задать вопрос']
                    ]];

                    $replyMarkup = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($buttons, true, false, true);
                    $bot->sendMessage($message->getChat()->getId(), 'Вы успешно зарегистрировались: уведомления будут дублироваться в этот чат-бот', null, false, null, $replyMarkup);
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
