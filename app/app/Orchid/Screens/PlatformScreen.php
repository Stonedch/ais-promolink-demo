<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class PlatformScreen extends Screen
{
    public function query(): iterable
    {
        return [];
    }

    public function name(): ?string
    {
        return config('app.name');
    }

    public function description(): ?string
    {
        return '';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Перейти на сайт')
                ->route('web.index.index')
                ->icon('bs.globe'),
        ];
    }

    public function layout(): iterable
    {
        return [];
    }
}
