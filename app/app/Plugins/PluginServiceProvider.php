<?php

namespace App\Plugins;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Orchid\Support\Facades\Dashboard;

abstract class PluginServiceProvider extends ServiceProvider
{
    protected $pluginName;

    public abstract static function getPluginName(): string;
    public abstract static function getPluginDescription(): string;

    public static function getMenu(): array
    {
        return [];
    }

    public static function getPermissions(): array
    {
        return [];
    }

    public static function isActive(): bool
    {
        return in_array(static::class, PluginServiceSupport::getActiveServices()->toArray());
    }

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
        // Загрузка обычных web-маршрутов с обязательным middleware 'web'
        $webRoutes = $this->getPluginPath('Routes/web.php');
        if (file_exists($webRoutes)) {
            Route::middleware(['web']) // обязательно для сессий и авторизации
                ->group($webRoutes);
        }

        // Загрузка маршрутов для Orchid Platform (админка)
        $platformRoutes = $this->getPluginPath('Routes/platform.php');
        if (file_exists($platformRoutes)) {
            Route::domain((string) config('platform.domain')) // убедитесь, что домен совпадает
                ->prefix(Dashboard::prefix('/'))
                ->middleware(config('platform.middleware.private', ['web', 'platform'])) // обязательно наличие 'web'
                ->group($platformRoutes);
        }
    }

    protected function loadViews()
    {
        $viewsPath = $this->getPluginPath('Views');
        if (file_exists($viewsPath)) {
            $this->loadViewsFrom($viewsPath, $this->pluginName);
        }
    }

    protected function loadMigrations()
    {
        $migrationsPath = $this->getPluginPath('Migrations');
        if (file_exists($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }
}
