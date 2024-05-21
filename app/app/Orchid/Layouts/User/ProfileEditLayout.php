<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class ProfileEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Input::make('user.phone')
                ->type('text')
                ->title('Номер телефона')
                ->disabled(),

            Input::make('user.email')
                ->type('text')
                ->max(255)
                ->required()
                ->title('E-mail'),

            Input::make('user.last_name')
                ->type('text')
                ->max(255)
                ->required()
                ->title('Фамилия'),

            Input::make('user.first_name')
                ->type('text')
                ->max(255)
                ->required()
                ->title('Имя'),

            Input::make('user.middle_name')
                ->type('text')
                ->max(255)
                ->required()
                ->title('Отчество'),
        ];
    }
}
