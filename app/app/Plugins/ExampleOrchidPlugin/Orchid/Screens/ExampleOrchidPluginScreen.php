<?php

declare(strict_types=1);

namespace App\Plugins\ExampleOrchidPlugin\Orchid\Screens;

use Orchid\Screen\Screen;

class ExampleOrchidPluginScreen extends Screen
{
    public function query(): iterable
    {
        return [];
    }

    public function name(): ?string
    {
        return 'Example Orchid Plugin';
    }

    public function layout(): iterable
    {
        return [];
    }
}
