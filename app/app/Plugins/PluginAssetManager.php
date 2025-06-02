<?php

namespace App\Plugins;

use Illuminate\Support\Facades\File;

class PluginAssetManager
{
    public static function linkAllAssets(): void
    {
        $pluginsPath = app_path('Plugins');
        $publicBasePath = public_path('owns');

        if (!is_dir($pluginsPath)) {
            return;
        }

        foreach (scandir($pluginsPath) as $plugin) {
            if (in_array($plugin, ['.', '..'])) {
                continue;
            }

            $pluginAssetsPath = $pluginsPath . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . 'Assets';
            $publicPluginPath = $publicBasePath . DIRECTORY_SEPARATOR . $plugin;

            if (is_dir($pluginAssetsPath) && !is_link($publicPluginPath)) {
                File::ensureDirectoryExists($publicBasePath);
                symlink($pluginAssetsPath, $publicPluginPath);
            }
        }
    }
}
