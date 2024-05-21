<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    public function query(): iterable
    {
        return [];
    }

    public function name(): ?string
    {
        return 'Главная';
    }

    public function description(): ?string
    {
        return '';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
        ];
    }
}
