<?php

namespace App\Services\FileSystems;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class Zipper
{
    protected ZipArchive $zip;

    public function __construct()
    {
        $this->zip = new ZipArchive();
    }

    public function zipFolder(string $source, string $destination): string
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        if (!$this->zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                    continue;
                }

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $this->zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file) === true) {
                    $this->zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $this->zip->addFromString(basename($source), file_get_contents($source));
        }

        return $this->zip->close();
    }
}
