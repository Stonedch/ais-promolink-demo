<?php

namespace App\Plugins;

use Illuminate\Support\ServiceProvider;

abstract class PluginServiceProvider extends ServiceProvider
{
    protected $pluginName;

    public function register()
    {
        $this->mergeConfigFrom(
            $this->getPluginPath('Config/config.php'),
            "plugins.{$this->pluginName}"
        );
    }

    public function boot()
    {
        $this->loadRoutes();
        $this->loadViews();
        $this->loadMigrations();
    }

    protected abstract function pluginBoot(): void;

    protected function getPluginPath($path = ''): string
    {
        return app_path("Plugins/{$this->pluginName}/{$path}");
    }

    protected function loadRoutes()
    {
        $webRoutes = $this->getPluginPath('Routes/web.php');
        if (file_exists($webRoutes)) $this->loadRoutesFrom($webRoutes);
    }

    protected function loadViews()
    {
        $viewsPath = $this->getPluginPath('Views');
        if (file_exists($viewsPath)) $this->loadViewsFrom($viewsPath, $this->pluginName);
    }

    protected function loadMigrations()
    {
        $migrationsPath = $this->getPluginPath('Migrations');
        if (file_exists($migrationsPath)) $this->loadMigrationsFrom($migrationsPath);
    }
}
