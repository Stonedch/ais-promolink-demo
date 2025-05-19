<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Plugins\PluginServiceSupport;
use Illuminate\Support\Collection;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    protected Collection $activePlugins;

    public function query(): iterable
    {
        $this->activePlugins = PluginServiceSupport::getActiveServices()
            ->map(fn(string $service): object => (object) [
                'name' => $service::getPluginName(),
                'description' => $service::getPluginDescription()
            ]);

        return [
            'activePlugins' => $this->activePlugins,
        ];
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
        return [
            Layout::table('activePlugins', [
                TD::make('name', 'Название')->render(fn(object $service) => $service->name),
                TD::make('description', 'Описание')->render(fn(object $service) => $service->description),
            ])->title('Включенные плагины')->canSee(empty($this->activePlugins->count()) == false),
        ];
    }
}
