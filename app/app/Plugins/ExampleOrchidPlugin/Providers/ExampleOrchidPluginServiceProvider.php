<?php

namespace App\Plugins\ExampleOrchidPlugin\Providers;

use App\Plugins\PluginServiceProvider;

class ExampleOrchidPluginServiceProvider extends PluginServiceProvider
{
    protected $pluginName = 'ExampleOrchidPlugin';

    public function boot()
    {
        parent::boot();
    }

    protected function pluginBoot(): void {}
}
