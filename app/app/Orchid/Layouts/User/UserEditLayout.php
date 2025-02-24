<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class UserEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Input::make('user.phone')
                ->type('text')
                ->required()
                ->title('Номер телефона'),

            Input::make('user.email')
                ->type('text')
                ->max(255)
                ->title('E-mail'),

            Input::make('user.last_name')
                ->type('text')
                ->max(255)
                ->title('Фамилия'),

            Input::make('user.first_name')
                ->type('text')
                ->max(255)
                ->title('Имя'),

            Input::make('user.middle_name')
                ->type('text')
                ->max(255)
                ->title('Отчество'),

            Cropper::make('user.attachment_id')
                ->targetId()
                ->title('Аватар'),

            CheckBox::make('user.is_active')
                ->sendTrueOrFalse()
                ->title('Активность пользователя'),
        ];
    }
}
