<?php

namespace App\Collections;

use App\Services\FileSystems\Zipper;
use Illuminate\Support\Collection;
use Orchid\Attachment\Models\Attachment;

class AttachmentCollection
{
    private Collection $attachments;

    public function __construct(?Collection $identifiers = null)
    {
        if (empty($identifiers) == false && $identifiers->count()) {
            $this->set($identifiers);
        }
    }

    public function get(): Collection
    {
        return $this->attachments;
    }

    public function set(Collection $identifiers): void
    {
        $this->attachments = Attachment::whereIn('id', $identifiers)->get();
    }

    public function filepaths(): Collection
    {
        $filepaths = [];
        $this->attachments->map(
            fn(Attachment $attachment) => $filepaths[] = self::getFilepathOf($attachment)
        );
        return collect($filepaths);
    }

    public function zip(?string $filename = null)
    {
        $filename = empty($filename) ? now()->getTimestamp() : $filename;
        $filename = "{$filename}.zip";
        $directory = storage_path("app/private/exports/");
        $filepath = $directory . $filename;

        $rootPath = storage_path('app/private/archives/');
        $rootFolderName = now()->format('d-m-Y-H-i-s');
        $rootFolderPath = "{$rootPath}{$rootFolderName}/";

        mkdir($rootFolderPath);

        $this->createDotFile($rootFolderPath);

        $this->get()->map(function (Attachment $attachment) use ($rootFolderPath) {
            copy(
                self::getFilepathOf($attachment),
                $rootFolderPath . now()->getTimestamp() . '-' . $attachment->original_name
            );
        });

        (new Zipper())->zipFolder($rootFolderPath, $filepath);

        return $filepath;
    }

    private static function getFilepathOf(Attachment $attachment): string
    {
        $filename = "{$attachment->name}.{$attachment->extension}";
        $filepath = "app/{$attachment->disk}/{$attachment->path}{$filename}";
        $filepath = storage_path($filepath);
        return $filepath;
    }

    private function createDotFile(string $folderPath, ?string $content = null)
    {
        $content = empty($content) ? 'dotfile' : $content;
        $filename = 'dot.txt';
        $filepath = $folderPath . $filename;
        $file = fopen($filepath, 'a');
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fwrite($file, $content . PHP_EOL);
        fclose($file);
    }
}
