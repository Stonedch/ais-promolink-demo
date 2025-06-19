<?php

declare(strict_types=1);

namespace App\Orchid\Screens\BotUserQuestion;

use App\Helpers\PhoneNormalizer;
use App\Models\BotUser;
use App\Models\BotUserNotification;
use App\Models\BotUserQuestion;
use App\Models\User;
use App\Orchid\Components\DateTimeRender;
use App\Orchid\Fields\Button;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use PHPUnit\Event\Code\Throwable;

class BotUserQuestionListScreen extends Screen
{
    public ?LengthAwarePaginator $questions = null;
    public ?Collection $botUsers = null;
    public ?Collection $users = null;

    public function query(): iterable
    {
        $questions = BotUserQuestion::withTrashed()->filters()->defaultSort('id', 'DESC')->paginate(50);
        $botUsers = BotUser::whereIn('id', $questions->pluck('bot_user_id'))->get();
        $users = User::whereIn('id', $botUsers->pluck('user_id'))->get();

        return [
            'questions' => $questions,
            'botUsers' => $botUsers,
            'users' => $users,
        ];
    }

    public function name(): ?string
    {
        return 'Вопросы бот-пользователей';
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
            Layout::table('questions', [
                TD::make('_actions', 'Действия')
                    ->width(100)
                    ->render(fn(BotUserQuestion $question) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Button::make(empty($question->deleted_at) ? 'Закрыть вопрос' : 'Удалить вопрос')
                                ->icon('bs.trash3')
                                ->confirm(empty($question->deleted_at) ? 'Вопрос будет закрыт!' : 'Вопрос будет удален!')
                                ->method('remove', ['id' => $question->id]),
                            Link::make('Ответить')
                                ->icon('bs.link')
                                ->href(route("platform.bot-notifications", [
                                    'tab' => empty($question->getUserIdentifier()) ? 'Песонализированная по бот-пользователю' : 'Персонализированная',
                                    'uid' => $question->getUserIdentifier(),
                                    'buid' => $question->bot_user_id
                                ])),
                        ])),

                TD::make('id', '#')
                    ->width('100')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort(),

                TD::make('_status', 'Статус')
                    ->width(100)
                    ->render(function (BotUserQuestion $question) {
                        return empty($question->deleted_at)
                            ? '<span class="badge bg-primary">В работе</span>'
                            : '<span class="badge bg-dark">Закрыт</span>';
                    }),

                TD::make('bot_user_id', 'Пользователь')
                    ->width(200)
                    ->render(function (BotUserQuestion $question) {
                        try {
                            $botUser = $this->botUsers->where('id', $question->bot_user_id)->first();
                            throw_if(empty($botUser));
                            $user = $this->users->where('id', $botUser->user_id)->first();
                            if (empty($user)) return PhoneNormalizer::humanizePhone($botUser->phone);
                            return $user->getFullname();
                        } catch (Throwable | Exception) {
                            return '-';
                        }
                    }),

                TD::make('question', 'Вопрос')
                    ->width(300),

                TD::make('created_at', 'Создано')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort()
                    ->width(200),
            ]),
        ];
    }

    public function remove(Request $request): void
    {
        $question = BotUserQuestion::withTrashed()->find($request->input('id'));

        if (empty($question->deleted_at)) {
            $question->delete();
            Toast::info('Вопрос успешно закрыт');
        } else {
            $question->forceDelete();
            Toast::info('Вопрос успешно удален');
        }
    }
}
