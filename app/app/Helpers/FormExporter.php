<?php

namespace App\Helpers;

use App\Models\Departament;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormResult;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use ZipArchive;

class FormExporter
{
    protected static string $separator = '	';

    public static function exportArchiveBy(Form $form): string
    {
        $events = Event::where('form_id', $form->id)->orderBy('id', 'desc')->get();
        $results = FormResult::query()->whereIn('event_id', $events->pluck('id'))->orderBy('index', 'asc')->get();
        $departaments = Departament::whereIn('id', $events->pluck('departament_id'))->get();

        $rootFolderName = date('d-m-Y-H-i-s', time());
        $rootPath = storage_path('app/private/archives/');
        $rootFolderPath = "{$rootPath}{$rootFolderName}/";

        mkdir($rootFolderPath, recursive: true);

        $filename = "dot.txt";
        $filepath = $rootFolderPath . $filename;
        $file = fopen($filepath, 'a');
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fwrite($file, 'Форма: ' . $form->name . PHP_EOL);
        fwrite($file, 'Дата: ' . date('d.m.Y H:i:s') . PHP_EOL);
        fwrite($file, 'Количество событий в выгрузке: ' . $events->count() . PHP_EOL);
        fwrite($file, 'Количество учреждений в выгрузке: ' . $departaments->count() . PHP_EOL);
        fclose($file);

        foreach ($departaments as $departament) {
            $departamentFolderName = $departament->name;
            $departamentFolderPath = $rootFolderPath . "$departamentFolderName/";

            mkdir($departamentFolderPath);

            foreach ($events->where('departament_id', $departament->id) as $event) {
                $structure = json_decode($event->form_structure);
                $headers = collect($structure->fields)->sortBy('sort')->pluck('name', 'id')->toArray();
                $values = [];

                if (empty($event->filled_at) == false) {
                    $eventResults = $results->where('event_id', $event->id);

                    foreach ($headers as $fid => $header) {
                        foreach ($eventResults->where('field_id', $fid) as $result) {
                            $values[$result->index][strval($result->field_id)] = $result->value;
                        }
                    }
                }

                $createdAt = date('d.m.Y-H.i.s', strtotime($event->created_at->toString()));
                $filename = "csv-{$createdAt}.csv";
                $filepath = $departamentFolderPath . $filename;

                $file = fopen($filepath, 'a');

                try {
                    fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                    fwrite($file, implode(self::$separator, $headers) . PHP_EOL);

                    foreach ($values as $slice) {
                        $counter = 0;

                        foreach (array_keys($headers) as $id) {
                            $divider = ++$counter < count($headers) ? self::$separator : PHP_EOL;
                            $cell = isset($slice[$id]) ? $slice[$id] : null;
                            fwrite($file, $cell . $divider);
                        }
                    }
                } finally {
                    fclose($file);
                }

                // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
                // $excel = $reader->load($filepath);

                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setDelimiter(self::$separator);
                $excel = $reader->load($filepath);

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
                $xlsxFilename = "xlsx-$createdAt.xlsx";
                $xlsxFilepath = $departamentFolderPath . $xlsxFilename;
                $writer->save($xlsxFilepath);
            }
        }

        $zipFilename = "{$rootFolderName}.zip";
        $zipPath = "{$rootPath}{$zipFilename}";

        self::zip($rootFolderPath, $zipPath);

        return $zipPath;
    }

    private static function zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
}
