<?php

namespace App\Console\Commands\Plugins;

use Illuminate\Console\Command;
use App\Plugins\PluginAssetManager;

class PluginLinkAssets extends Command
{
    protected $signature = 'plugins:link-assets';
    protected $description = 'Создает симлинки для ассетов всех плагинов в public/plugins';

    public function handle(): int
    {
        $this->info('Создание симлинков для ассетов плагинов...');

        try {
            PluginAssetManager::linkAllAssets();
            $this->info('✅ Симлинки успешно созданы!');
        } catch (\Exception $e) {
            $this->error('❌ Ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
