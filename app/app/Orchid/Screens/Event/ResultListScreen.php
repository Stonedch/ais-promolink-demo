<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Event;

use App\Helpers\PhoneNormalizer;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormResult;
use App\Orchid\Components\DateTimeRender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

class ResultListScreen extends Screen
{
    public function query(Event $event): iterable
    {
        $formResults = FormResult::query()
            ->where('event_id', $event->id)
            ->filters()
            ->defaultSort('id', 'desc')
            ->paginate();

        return [
            'formResults' => $formResults,
        ];
    }

    public function name(): ?string
    {
        return 'Результаты';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.form_results.list',
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('formResults', [
                TD::make('id', '#')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort()
                    ->defaultHidden()
                    ->width(100),

                TD::make('user_id', 'Пользователь')
                    ->sort()
                    ->width(200)
                    ->render(function (FormResult $formResult) {
                        try {
                            $user = $this->users->find($formResult->user_id);
                            $phone = PhoneNormalizer::humanizePhone($user->phone);
                            return "[$user->id] $phone";
                        } catch (Throwable $e) {
                            return '-';
                        }
                    }),

                TD::make('user_id', 'Событие')
                    ->sort()
                    ->width(200),

                TD::make('user_id', 'Пользователь')
                    ->sort()
                    ->width(200)
                    ->render(function (FormResult $formResult) {
                        try {
                            $field = $this->fields->where('id', $formResult->field_id)->first();
                            return "[$field->id] $field->name";
                        } catch (Throwable $e) {
                            return '-';
                        }
                    }),

                TD::make('value', 'Значение')
                    ->sort()
                    ->width(200),

                TD::make('created_at', 'Создано')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort()
                    ->width(200),

                TD::make('updated_at', 'Обновлено')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort()
                    ->width(200),
            ]),
        ];
    }
}
