<?php

namespace App\Plugins;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Orchid\Platform\Providers\RouteServiceProvider;
use Orchid\Support\Facades\Dashboard;

abstract class PluginServiceProvider extends ServiceProvider
{
    protected $pluginName;

    public function register()
    {
        $config = $this->getPluginPath('Config/config.php');

        if (file_exists($config)) {
            $this->mergeConfigFrom(
                $config,
                "plugins.{$this->pluginName}"
            );
        }
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

        $platformRoutes = $this->getPluginPath('Routes/platform.php');
        if (file_exists($platformRoutes)) {
            Route::domain((string) config('platform.domain'))
                ->prefix(Dashboard::prefix('/'))
                ->middleware(config('platform.middleware.private'))
                ->group($platformRoutes);
        }
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
