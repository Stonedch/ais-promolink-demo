<?php

declare(strict_types=1);

namespace App\Console\Commands\Cleaners;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FolderCleanerCommand extends Command
{
    protected $signature = 'cleaners:folder:clean {disk} {path}';

    public function handle(): void
    {
        $storage = Storage::disk('private');

        foreach ($storage->files($this->argument('path'), recursive: true) as $file) {
            $storage->delete($file);
        }

        foreach ($storage->directories($this->argument('path'), recursive: true) as $directory) {
            $storage->deleteDirectory($directory);
        }
    }
}
