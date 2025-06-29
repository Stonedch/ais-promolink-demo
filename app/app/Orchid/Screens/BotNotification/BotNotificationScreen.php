<?php

declare(strict_types=1);

namespace App\Orchid\Screens\BotNotification;

use App\Enums\BotUserNotificationStatus;
use App\Exceptions\HumanException;
use App\Helpers\BotHelpers\TelegramBotHelper;
use App\Helpers\FormExporter;
use App\Helpers\FormHelper;
use App\Helpers\PhoneNormalizer;
use App\Models\BotUser;
use App\Models\BotUserNotification;
use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\User;
use App\Orchid\Components\DateTimeRender;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use PHPUnit\Event\Code\Throwable;

class BotNotificationScreen extends Screen
{
    public ?LengthAwarePaginator $notifications = null;
    public ?Collection $botUsers = null;
    public ?Collection $users = null;

    public function query(): iterable
    {
        $notifications = BotUserNotification::withTrashed()->filters()->defaultSort('id', 'DESC')->paginate();
        $botUsers = BotUser::whereIn('id', $notifications->pluck('bot_user_id'))->get();
        $users = User::whereIn('id', $botUsers->pluck('user_id'))->get();

        return [
            'notifications' => $notifications,
            'botUsers' => $botUsers,
            'users' => $users,
        ];
    }

    public function name(): ?string
    {
        return 'Бот-уведомления';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.bot_users.base',
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::tabs([
                'По учреждениям' => Layout::rows([
                    Select::make('notification.departament_type_id')
                        ->empty('-')
                        ->options(fn() => DepartamentType::pluck('name', 'id'))
                        ->multiple()
                        ->title('Тип учреждения'),

                    TextArea::make('notification.message')
                        ->title('Сообщение')
                        ->rows(32),

                    Button::make('Создать')
                        ->icon('bs.check-circle')
                        ->method('notify')
                ]),
                'Персонализированная' => Layout::rows([
                    Select::make('notification.byuser.user_id')
                        ->empty('-')
                        ->value([request()->input('uid', null)])
                        ->options(function () {
                            $options = [];

                            User::all()->map(function (User $user) use (&$options) {
                                $options[$user->id] = "#{$user->id}, {$user->getFullname()}";
                            });

                            return $options;
                        })
                        ->multiple()
                        ->title('Пользователь'),

                    TextArea::make('notification.byuser.message')
                        ->title('Сообщение')
                        ->rows(32),

                    Button::make('Создать')
                        ->icon('bs.check-circle')
                        ->method('notifyByUser')
                ]),
                'Персонализированная по бот-пользователю' => Layout::rows([
                    Select::make('notification.bybotuser.id')
                        ->empty('-')
                        ->value([request()->input('buid', null)])
                        ->options(function () {
                            $options = [];

                            BotUser::all()->map(function (BotUser $user) use (&$options) {
                                $options[$user->id] = "#{$user->id}, {$user->phone}";
                            });

                            return $options;
                        })
                        ->multiple()
                        ->title('Пользователь'),

                    TextArea::make('notification.bybotuser.message')
                        ->title('Сообщение')
                        ->rows(32),

                    Button::make('Создать')
                        ->icon('bs.check-circle')
                        ->method('notifyByBotUser')
                ])
            ])->activeTab(request()->input('tab', 'По учреждениям')),

            Layout::table('notifications', [
                TD::make('id', '#')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort()
                    ->defaultHidden()
                    ->width(100),

                TD::make('bot_user_id', 'Пользователь')
                    ->width(200)
                    ->render(function (BotUserNotification $notification) {
                        try {
                            $botUser = $this->botUsers->where('id', $notification->bot_user_id)->first();
                            throw_if(empty($botUser));
                            $user = $this->users->where('id', $botUser->user_id)->first();
                            return empty($user) ? PhoneNormalizer::humanizePhone($botUser->phone) : $user->getFullname();
                        } catch (Throwable | Exception) {
                            return '-';
                        }
                    }),

                TD::make('data', 'Сообщение')
                    ->width(200)
                    ->render(function (BotUserNotification $notification) {
                        try {
                            $data = json_decode($notification->data);
                            return \Str::words($data->message, 32);
                        } catch (Throwable) {
                            return '-';
                        }
                    }),

                TD::make('status', 'Статус')
                    ->width(200)
                    ->render(function (BotUserNotification $notification) {
                        try {
                            $status = BotUserNotificationStatus::from($notification->status);
                            return $status->bootstrapme();
                        } catch (Throwable) {
                            return '-';
                        }
                    }),

                TD::make('status_message', 'Тех. сообщение')
                    ->width(200)
                    ->render(function (BotUserNotification $notification) {
                        try {
                            return \Str::words($notification->status_message, 32);
                        } catch (Throwable) {
                            return '-';
                        }
                    }),

                TD::make('created_at', 'Создано')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort()
                    ->width(200),

                TD::make('deleted_at', 'Отправлено')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort()
                    ->width(200),
            ]),
        ];
    }

    public function notify(Request $request)
    {
        try {
            foreach ($request->input('notification.departament_type_id', []) as $departamentTypeId) {
                $message = $request->input('notification.message', null);

                throw_if(empty($departamentTypeId), new HumanException('Поле "Тип учреждения" обязательно к заполнению!'));
                throw_if(empty($message), new HumanException('Поле "Сообщение" обязательно к заполнению!'));

                $departaments = Departament::where('departament_type_id', $departamentTypeId)->get();
                $users = User::whereIn('departament_id', $departaments->pluck('id'))->get();

                foreach ($users as $user) {
                    try {
                        TelegramBotHelper::notify($user, 'Уведомление', $message);
                    } catch (Exception) {
                        continue;
                    }
                }
            }

            Toast::success('Успешно');
        } catch (HumanException $e) {
            Toast::error($e->getMessage());
        } catch (Throwable $e) {
            Toast::error("Внутренняя ошибка: {$e->getMessage()}");
        }
    }

    public function notifyByUser(Request $request)
    {
        try {
            $users = User::whereIn('id', $request->input('notification.byuser.user_id', []))->get();
            $message = $request->input('notification.byuser.message', null);

            throw_if(empty($users->count()), new HumanException('Поле "Пользователь" обязательно к заполнению!'));
            throw_if(empty($message), new HumanException('Поле "Сообщение" обязательно к заполнению!'));

            foreach ($users as $user) {
                try {
                    TelegramBotHelper::notify($user, 'Уведомление', $message);
                } catch (Exception) {
                    continue;
                }
            }

            Toast::success('Успешно');
        } catch (HumanException $e) {
            Toast::error($e->getMessage());
        } catch (Throwable $e) {
            Toast::error("Внутренняя ошибка: {$e->getMessage()}");
        }
    }

    public function notifyByBotUser(Request $request)
    {
        try {
            $botUsers = BotUser::whereIn('id', $request->input('notification.bybotuser.id', []))->get();
            $message = $request->input('notification.bybotuser.message', null);

            throw_if(empty($botUsers->count()), new HumanException('Поле "Пользователь" обязательно к заполнению!'));
            throw_if(empty($message), new HumanException('Поле "Сообщение" обязательно к заполнению!'));

            foreach ($botUsers as $user) {
                try {
                    TelegramBotHelper::notifyBot($user, 'Уведомление', $message);
                } catch (Exception) {
                    continue;
                }
            }

            Toast::success('Успешно');
        } catch (HumanException $e) {
            Toast::error($e->getMessage());
        } catch (Throwable $e) {
            Toast::error("Внутренняя ошибка: {$e->getMessage()}");
        }
    }
}
