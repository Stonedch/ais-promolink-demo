<?php

namespace App\Plugins\ExamplePlugin\Providers;

use App\Plugins\PluginServiceProvider;

class ExamplePluginServiceProvider extends PluginServiceProvider
{
    protected $pluginName = 'ExamplePlugin';

    public function boot()
    {
        parent::boot();
    }

    protected function pluginBoot(): void {}
}
