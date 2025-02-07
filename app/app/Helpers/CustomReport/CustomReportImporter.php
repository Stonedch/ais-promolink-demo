<?php

namespace App\Helpers\CustomReport;

use App\Helpers\BotHelpers\TelegramBotHelper;
use App\Models\CustomReport;
use App\Models\CustomReportType;
use App\Models\User;
use Exception;
use Orchid\Attachment\Models\Attachment;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\Console\Command\Command;
use Throwable;
use App\Models\CustomReportData;
use Illuminate\Support\Facades\Artisan;

setlocale(LC_ALL, 'ru_RU.UTF-8');

class CustomReportImporter
{
    protected const AVAILABLE_EXTENSIONS = [
        'xls',
        'xlsx',
        'xlsm'
    ];

    protected bool $debug = false;
    protected ?Command $console;

    protected $report_types;
    protected $timestamp;
    protected $obUsers;

    public function __construct(?Command $console = null)
    {
        if ($console) {
            $this->setConsole($console);
        }

        $this->timestamp = time();
        $this->obUsers = User::query()->select(["id", 'departament_id'])->get();
    }

    public function handle(int $take = 10): void
    {
        $reports = CustomReport::query()
            ->orderBy('id', 'desc')
            ->where('worked', false)
            ->whereNotNull('user_id')
            ->take($take)
            ->get();

        if ($this->debug == false) {
            $reports->map(function (CustomReport $report) {
                $report->worked = true;
                $report->save();
            });
        }

        $reportTypes = CustomReportType::query()
            ->whereIn('id', $reports->pluck('custom_report_type_id'))
            ->get()
            ->keyBy('id');

        $attachments = Attachment::query()
            ->whereIn('id', $reportTypes->pluck('attachment_id'))
            ->get()
            ->keyBy('id');

        $reports->map(function (CustomReport $report) use ($reportTypes, $attachments) {
            try {
                $reportType = $reportTypes->get($report->custom_report_type_id);
                $template = $attachments->get($reportType->attachment_id);

                if ($reportType->is_freelace) {
                    throw_if(empty($reportType->command), new Exception('Внештатная команда не распознана'));
                    Artisan::call($reportType->command);
                } else {
                    throw_if(empty($template), new Exception('Ошибка поиска шаблона'));

                    throw_if(
                        in_array($template->extension, self::AVAILABLE_EXTENSIONS) == false,
                        new Exception("Ошибка формата шаблона (.$template->extension)")
                    );

                    $templateFilepath = "app/{$template->disk}/{$template->path}{$template->name}.{$template->extension}";
                    $templateFilepath = storage_path($templateFilepath);

                    $this->load_report($report, $templateFilepath);
                }

                if ($this->console) {
                    $this->console->comment("report:{$report->id} is ready");
                }
            } catch (Throwable | Exception $e) {
                if ($this->console) {
                    $this->console->error($e->getMessage());
                }
            }
        });
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function setConsole(?Command $console): void
    {
        $this->console = $console;
    }

    private function insert_data_into_bd($report, $arDocument)
    {
        $type = CustomReportType::find($report->custom_report_type_id);

        if ($type->is_updatable) {
            $this->update($report, $arDocument);
        } else {
            $this->insert($report, $arDocument);
        }

        echo "loaded #" . $report->id . "\r\n";
    }

    private function insert(CustomReport $report, array $document): void
    {
        $arInsert = [];
        $departament_id = $this->obUsers->find($report->user_id)->departament_id;

        foreach ($document as $doc) {
            $arInsert[] = [
                "departament_id" => $departament_id,
                "created_at" => $report->created_at,
                "user_id" => $report->user_id,
                "custom_report_type_id" => $report->custom_report_type_id,
                "page" => $doc['page'],
                "row" => $doc['row'],
                "column" => $doc['col'],
                "value" => $doc['val'],
                "type" => $doc['type'],
                "loaded_at" => date("Y-m-d H:i:s", $this->timestamp),
            ];
        }

        CustomReportData::insert($arInsert);
    }

    private function update(CustomReport $report, array $document): void
    {
        foreach ($document as $doc) {
            $reportData = CustomReportData::query()
                ->where('user_id', $report->user_id)
                ->where('custom_report_type_id', $report->custom_report_type_id)
                ->where('row', $doc['row'])
                ->where('column', $doc['col'])
                ->first();

            if (empty($reportData)) {
                (new CustomReportData())->fill([
                    "departament_id" => $departament_id,
                    "created_at" => $report->created_at,
                    "user_id" => $report->user_id,
                    "custom_report_type_id" => $report->custom_report_type_id,
                    "page" => $doc['page'],
                    "row" => $doc['row'],
                    "column" => $doc['col'],
                    "value" => $doc['val'],
                    "type" => $doc['type'],
                    "loaded_at" => date("Y-m-d H:i:s", $this->timestamp),
                ])->save();

                continue;
            }

            $reportData->fill([
                "value" => $doc['val'],
            ])->save();
        }
    }

    private function get_xls_reader_by_filepath($filepath)
    {
        $reader = new Xlsx();
        try {
            $xls = $reader->load($filepath);
        } catch (Throwable $e) {
            return (string)$e;
        }
        return $xls;
    }

    private function get_xls_reader_by_attachment_id($attachment_id)
    {
        $file = Attachment::find($attachment_id);
        if (!in_array($file->extension, [
            "xls",
            "xlsx",
            "xlsm"
        ])) {
            return "Некорректный формат (.$file->extension)";
        }
        $filepath = storage_path("app/{$file->disk}/{$file->path}{$file->name}.{$file->extension}");

        return $this->get_xls_reader_by_filepath($filepath);
    }

    private function error_handler(string $error)
    {
        echo $error . "\r\n";

        try {
            TelegramBotHelper::notify($user, $title, $body);
        } catch (Throwable | Exception) {
            echo "Telegram пользователь не найден" . "\r\n";
        }
    }

    private function read_xls_into_array($xls)
    {
        $sheet_count = $xls->getSheetCount();
        $page_number = 0;
        $arResult = [];

        while ($page_number < $sheet_count) {
            $sheet = $xls->getSheet($page_number);
            $page_end_coord = [
                'col' => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestDataColumn()),
                'row' => $sheet->getHighestDataRow(),
            ];

            $read_now = [
                'col' => 1,
                'row' => 1,
            ];


            $arResult = [];

            // $arKnownValues = ["s", "n", "f", "null"];
            while ($read_now['row'] < $page_end_coord['row']) {
                $read_now['col'] = 1;
                while ($read_now['col'] < $page_end_coord['col']) {
                    $type = $sheet->getCell([$read_now['col'], $read_now['row']])->getDataType();
                    $read_now['col']++;
                    if ($type == 'null') continue;

                    /*if(!in_array($type,$arKnownValues)) {
                        echo "tick ".$page_number."|".$read_now['col'].":".$read_now['row']."\r\n";
                        var_dump($type); die();
                    }*/


                    $value = $sheet->getCell([$read_now['col'], $read_now['row']])->getFormattedValue();

                    if ($value == '') continue;

                    switch ($type) {
                        case "f":
                            $value = floatval(str_replace([" ", ","], "", $value));
                            break;
                        case "n":
                            $value = intval(str_replace([" ", ","], "", $value));
                            break;
                        default:
                            $value = trim($value);
                            break;
                    }

                    $arResult[] = [
                        'page' => $page_number,
                        'col' => $read_now['col'],
                        'row' => $read_now['row'],
                        'val' => $value,
                        'type' => $type
                    ];
                }
                $read_now['row']++;
            }
            $page_number++;
        }
        return $arResult;
    }

    private function verify_document_with_original($arDocument, $origPath)
    {
        static $arOriginal;
        if (!is_array($arOriginal)) {
            $arOriginal = [];
        }
        if (!array_key_exists($origPath, $arOriginal)) {
            $xls = $this->get_xls_reader_by_filepath($origPath);
            $arOriginal[$origPath] = $this->read_xls_into_array($xls);
        }


        $arTemp = [];
        foreach ($arDocument as $elem) {
            if (!array_key_exists($elem['page'], $arTemp)) {
                $arTemp[$elem['page']] = [];
            }
            if (!array_key_exists($elem['row'], $arTemp[$elem['page']])) {
                $arTemp[$elem['page']][$elem['row']] = [];
            }
            if (!array_key_exists($elem['col'], $arTemp[$elem['page']][$elem['row']])) {
                $arTemp[$elem['page']][$elem['row']][$elem['col']] = [
                    'val' => $elem['val'],
                    'type' => $elem['type'],
                ];
            }
        }

        $error = false;
        foreach ($arOriginal[$origPath] as $data) {
            if ($data['type'] != 's') continue;

            if (!array_key_exists($data['page'], $arTemp)) {
                echo "\r\nerror block is A\r\n";
                $error = true;
                break;
            }
            if (!array_key_exists($data['row'], $arTemp[$data['page']])) {
                echo "\r\nerror block is B\r\n";
                $error = true;
                break;
            }
            if (!array_key_exists($data['col'], $arTemp[$data['page']][$data['row']])) {
                echo "\r\nerror block is C\r\n";
                $error = true;
                break;
            }
            if (
                $arTemp[$data['page']][$data['row']][$data['col']]['val'] != $data['val'] or
                $arTemp[$data['page']][$data['row']][$data['col']]['type'] != $data['type']
            ) {

                // var_dump([
                //     $arTemp[$data['page']][$data['row']][$data['col']],
                //     $data['val']
                // ]);

                echo "\r\nerror block is D\r\n";
                $error = true;
                break;
            }
        }

        if ($error !== false) {
            return false;
        }

        return true;
    }

    private function load_report($report, $exampleDoc)
    {
        $xls = $this->get_xls_reader_by_attachment_id($report->attachment_id);
        if (is_string($xls)) {
            $this->error_handler($xls);
            return false;
        }

        $arDocument = $this->read_xls_into_array($xls);
        $res = $this->verify_document_with_original($arDocument, $exampleDoc);

        if ($res === false) {
            $this->error_handler("Некорректный формат документа!");
        } else {
            $this->insert_data_into_bd($report, $arDocument);
            $this->mark_report_as_worked($report);
        }
    }

    private function mark_report_as_worked($report)
    {
        $CustomReport = CustomReport::find($report->id);
        $CustomReport->worked = true;
        $CustomReport->save();
    }

    private function detect_type($report_id)
    {
        if (is_null($this->report_types)) {
            $this->report_types = CustomReportType::getList();
        }

        $report_type = $this->report_types->firstWhere("id", "=", $report_id);
        if (is_null($report_type)) {
            return false;
        } else {
            return $report_type->title;
        }
    }
}
