<?php

namespace App\Plugins\EntityLogger\Orchid\Screens;

use App\Plugins\EntityLogger\Models\EntityLog;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class EntityLogListScreen extends Screen
{
    public function name(): string
    {
        return 'Учет истории изменений сущностей';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.plugins.entity-logger.base',
        ];
    }

    public function query(): iterable
    {
        return [
            'logs' => EntityLog::paginate(),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('logs', [
                TD::make('id', '#'),
                TD::make('message', 'Тип'),
                TD::make('model', 'Модель'),
                TD::make('fields', 'Поля'),
                TD::make('user', 'Пользователь'),
                TD::make('ip', 'IP'),
                TD::make('created_at', 'Дата создания'),
            ]),
        ];
    }
}
