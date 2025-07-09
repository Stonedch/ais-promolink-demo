<?php

namespace App\Services\CustomReport;

use App\Enums\CustomReportLogType;
use App\Exceptions\UnStoringException;
use App\Services\Bot\TelegramBot;
use App\Models\CustomReport;
use App\Models\CustomReportType;
use App\Models\User;
use Exception;
use Orchid\Attachment\Models\Attachment;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Symfony\Component\Console\Command\Command;
use Throwable;
use App\Models\CustomReportData;
use App\Models\CustomReportLog;
use Illuminate\Support\Facades\Artisan;

setlocale(LC_ALL, 'ru_RU.UTF-8');
ini_set('memory_limit', '-1');

class CustomReportImporter extends Command
{
    protected const AVAILABLE_EXTENSIONS = [
        'xls',
        'xlsx',
        'xlsm'
    ];

    protected bool $debug = false;

    protected ?Command $console;
    public object $output;
    public object $input;

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

    public function handle(?int $take = null): void
    {
        $this->log(message: "Старт", type: CustomReportLogType::DEBUG, storing: false);

        $reports = CustomReport::query()
            ->orderBy('id', 'asc')
            ->whereNull('worked_at')
            ->where('worked', false)
            ->whereNotNull('user_id');

        if (empty($take) == false) {
            $reports = $reports->take($take);
        }

        $reports = $reports->get();

        $this->log(message: "Список отчетов получен", type: CustomReportLogType::DEBUG, storing: false);

        $reportTypes = CustomReportType::query()
            ->whereIn('id', $reports->pluck('custom_report_type_id'))
            ->get()
            ->keyBy('id');

        $this->log(message: "Список типов отчетов получен", type: CustomReportLogType::DEBUG, storing: false);

        $attachments = Attachment::query()
            ->whereIn('id', $reportTypes->pluck('attachment_id'))
            ->get()
            ->keyBy('id');

        $this->log(message: "Список документов получен", type: CustomReportLogType::DEBUG, storing: false);
        $this->log(message: "Старт обработки", type: CustomReportLogType::DEBUG, storing: false);

        $reports->map(function (CustomReport $report) use ($reportTypes, $attachments) {
            $reportType = $reportTypes->get($report->custom_report_type_id);

            // $memory = memory_get_usage() / 1024 / 1024;
            // $this->log(message: "ОЗУ: $memory мб", type: CustomReportLogType::ACCESS, storing: false);
            $this->log(message: "Получен отчет №{$report->id}", type: CustomReportLogType::DEBUG, storing: false);

            try {
                $template = $attachments->get($reportType->attachment_id);
                // $template = Attachment::find($reportType->attachment_id);

                if ($reportType->is_freelance) {
                    throw_if(
                        empty($reportType->command),
                        new UnStoringException('Внештатная команда не распознана')
                    );

                    Artisan::call($reportType->command, ['id' => $report->id], $this->output);
               } else {
                    throw_if(
                        empty($template),
                        new UnStoringException('Ошибка поиска шаблона')
                    );

                    throw_if(
                        in_array($template->extension, self::AVAILABLE_EXTENSIONS) == false,
                        new Exception("Ошибка формата шаблона (.$template->extension)")
                    );

                    $templateFilepath = "app/{$template->disk}/{$template->path}{$template->name}.{$template->extension}";
                    $templateFilepath = storage_path($templateFilepath);

                    $this->load_report($report, $templateFilepath);
                }

                $this->log(
                    message: 'is ready',
                    type: CustomReportLogType::ACCESS,
                    user: User::find($report->user_id),
                    customReport: $report,
                    customReportType: $reportType,
                    templateFilepath: @$templateFilepath
                );

                $report->worked = true;
                $report->save();
            } catch (UnStoringException $e) {
                $this->log(
                    message: $e->getMessage(),
                    type: CustomReportLogType::ERROR,
                    user: User::find($report->user_id),
                    customReport: $report,
                    customReportType: $reportType,
                    storing: false,
                );
            } catch (Throwable | Exception $e) {
                $this->log(
                    message: $e->getMessage(),
                    type: CustomReportLogType::ERROR_MESSAGE,
                    user: User::find($report->user_id),
                    customReport: $report,
                    customReportType: $reportType,
                    storing: true,
                );
            }

            $report->worked_at = now();
            $report->save();
        });
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function setConsoleOutput(object $output): void
    {
        $this->output = $output;
    }

    public function setConsoleInput(object $input): void
    {
        $this->input = $input;
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

        $this->log(
            message: 'loaded',
            type: CustomReportLogType::LOG,
            customReport: $report,
            customReportType: $type,
        );
    }

    private function insert(CustomReport $report, array $document): void
    {
        $arInsert = [];
        $departament_id = $this->obUsers->find($report->user_id)->departament_id;

        $this->log(message: 'Учреждение найдено', type: CustomReportLogType::DEBUG, storing: false);

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

        $take = 5000;
        $count = count($arInsert);

        $this->log(message: "Массив подготовлен ({$count})", type: CustomReportLogType::DEBUG, storing: false);

        if (empty($this->output) == false) {
            $progressBar = $this->output->createProgressBar($count);
            $progressBar->start();
        }

        foreach (array_chunk($arInsert, $take) as $chunk) {
            CustomReportData::insert($chunk);

            if (empty($this->output) == false) {
                $progressBar->advance($take);
            }
        }

        if (empty($this->output) == false) {
            $progressBar->finish();
        }

        if ($this->console) $this->console->comment("");
        $this->log(message: "Чанки загружены", type: CustomReportLogType::DEBUG, storing: false);

        $this->log(message: 'Массив загружен', type: CustomReportLogType::DEBUG, storing: false);
    }

    private function update(CustomReport $report, array $document): void
    {
        CustomReportData::query()
            ->where('user_id', $report->user_id)
            ->where('custom_report_type_id', $report->custom_report_type_id)
            ->get()
            ->map(fn(CustomReportData $reportData) => $reportData->delete());

        $this->log(message: 'Обновляемый отчет подготовлен под загрузку новых данных', type: CustomReportLogType::DEBUG, storing: false);

        $this->insert($report, $document);
    }

    private function get_xls_reader_by_filepath($filepath)
    {
        $reader = new Xlsx();

        try {
            $xls = $reader->load($filepath);
        } catch (Throwable $e) {
            try {
                $xls = (new Xls())->load($filepath);
            } catch (Throwable $e) {
                return (string)$e;
            }
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
        $this->log(message: "Получен файт на отчет \"{$filepath}\"", type: CustomReportLogType::DEBUG, storing: false);

        return $this->get_xls_reader_by_filepath($filepath);
    }

    private function error_handler(string $error)
    {
        $this->log(
            message: $error,
            type: CustomReportLogType::ERROR,
        );

        try {
            TelegramBot::notify($user, $title, $body);
        } catch (Throwable | Exception) {
            $this->log(
                message: 'Telegram пользователь не найден',
                type: CustomReportLogType::WARNING,
            );
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

            // $arKnownValues = ["s", "n", "f", "null"];
            while ($read_now['row'] < $page_end_coord['row']) {
                $read_now['col'] = 1;
                while ($read_now['col'] < $page_end_coord['col']) {
                    $type = $sheet->getCell([$read_now['col'], $read_now['row']])->getDataType();
                    $read_now['col']++;

                    try {
                        $value = $sheet->getCell([$read_now['col'], $read_now['row']])->getFormattedValue();
                    } catch (Throwable $e) {
                        $value = '';
                    }

                    if ($value == '') continue;
                    if ($value == 'null') continue;

                    switch ($type) {
                        case "f":
                            $value = floatval(str_replace([" ", ","], ["", ""], $value));
                            break;
                        case "n":
                            $value = intval(str_replace([" ", ","], ["", ""], $value));
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

    protected static array $arOriginal = [];

    private function verify_document_with_original($arDocument, $origPath, ?CustomReport $report = null, ?User $user = null)
    {
        if (!array_key_exists($origPath, self::$arOriginal)) {
            $xls = $this->get_xls_reader_by_filepath($origPath);
            self::$arOriginal[$origPath] = $this->read_xls_into_array($xls);
            $xls->disconnectWorksheets();
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
        foreach (self::$arOriginal[$origPath] as $data) {
            if ($data['type'] != 's') continue;
            if ($data['val'] == 0) continue;
            if ($data['val'] == '-') continue;

            if (!array_key_exists($data['page'], $arTemp)) {
                $cpage = $data['page'] + 1;
                // страницы нет в документе, которая присутствует в шаблоне (+ номер страницы data['page'] + 1)
                // $this->log(message: 'Отчет не соответствует структуре (A)', type: CustomReportLogType::ERROR);
                $this->log(
                    message: "Страница №{$cpage} отсутствует в загруженном в документе",
                    type: CustomReportLogType::ERROR_MESSAGE,
                    customReport: $report,
                    customReportType: CustomReportType::find($report->custom_report_type_id),
                    user: User::find($report->user_id)
                );
                $error = true;
                break;
            }
            if (!array_key_exists($data['row'], $arTemp[$data['page']])) {
                $cpage = $data['page'] + 1;
                $crow = $data['row'] + 1;
                // $this->log(message: 'Отчет не соответствует структуре (B)', type: CustomReportLogType::ERROR);
                // $this->log(message: 'Отчет не соответствует структуре (B)', type: CustomReportLogType::ERROR);
                // $this->log(message: "Строка №{$crow} отсутствует на странице №{$cpage} в загруженном в документе", type: CustomReportLogType::ERROR_MESSAGE);
                $this->log(
                    message: "Строка №{$crow} отсутствует на странице №{$cpage} в загруженном в документе",
                    type: CustomReportLogType::ERROR_MESSAGE,
                    customReport: $report,
                    customReportType: CustomReportType::find($report->custom_report_type_id),
                    user: User::find($report->user_id)
                );
                $error = true;
                break;
            }
            if (!array_key_exists($data['col'], $arTemp[$data['page']][$data['row']])) {
                $cpage = $data['page'] + 1;
                $crow = $data['row'] + 1;
                $ccolumn = $data['col'] + 1;
                // $this->log(message: 'Отчет не соответствует структуре (C)', type: CustomReportLogType::ERROR);
                // $this->log(message: "Колонка №{$ccolumn} отсутствует на странице №{$cpage} в колонке №{$crow} в загруженном в документе на стр. №", type: CustomReportLogType::ERROR_MESSAGE);
                $this->log(
                    message: "Колонка №{$ccolumn} отсутствует на странице №{$cpage} в строке №{$crow} в загруженном в документе",
                    type: CustomReportLogType::ERROR_MESSAGE,
                    customReport: $report,
                    customReportType: CustomReportType::find($report->custom_report_type_id),
                    user: User::find($report->user_id)
                );
                $error = true;
                break;
            }

            $coord = "[страница: \"{$data['page']}\"; колонка: {$data['col']}; строка: {$data['row']}]";
            $compareTypes = "[тип значение в документе: \"{$arTemp[$data['page']][$data['row']][$data['col']]['type']}\"; в шаблоне: \"{$data['type']}\"]";
            $compareValues = "[значение в документе: \"{$arTemp[$data['page']][$data['row']][$data['col']]['val']}\"; в шаблоне: \"{$data['val']}\"]";

            if ($data['type'] != 'null') {
                if (
                    $arTemp[$data['page']][$data['row']][$data['col']]['val'] != $data['val']
                ) {
                    // $this->log(
                    //     message: "Структура загруженного документа не соответствует образцу: {$coord} {$compareTypes} {$compareValues}",
                    //     type: CustomReportLogType::ERROR_MESSAGE,
                    // );
                    $this->log(
                        message: "Структура загруженного документа не соответствует образцу: {$coord} {$compareTypes} {$compareValues}",
                        type: CustomReportLogType::ERROR_MESSAGE,
                        customReport: $report,
                        customReportType: CustomReportType::find($report->custom_report_type_id),
                        user: User::find($report->user_id)
                    );

                    $error = true;
                    break;
                }
            } else {
                if (
                    $arTemp[$data['page']][$data['row']][$data['col']]['val'] != $data['val']
                ) {
                    $this->log(
                        message: "Структура загруженного документа не соответствует образцу: {$coord} {$compareTypes} {$compareValues}",
                        type: CustomReportLogType::ERROR_MESSAGE,
                        customReport: $report,
                        customReportType: CustomReportType::find($report->custom_report_type_id),
                        user: User::find($report->user_id)
                    );

                    $error = true;
                    break;
                }
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
        $this->log(message: 'Получен reader', type: CustomReportLogType::DEBUG, storing: false);

        if (is_string($xls)) {
            $this->error_handler($xls);
            return false;
        }

        $arDocument = $this->read_xls_into_array($xls);
        $xls->disconnectWorksheets();
        $this->log(message: 'Документ прочитан', type: CustomReportLogType::DEBUG, storing: false);

        $res = $this->verify_document_with_original($arDocument, $exampleDoc, $report);
        $this->log(message: 'Документ сравнен', type: CustomReportLogType::DEBUG, storing: false);

        if ($res === false) {
            throw new Exception('Структура загруженного документа не соответствует образцу!');
        } else {
            $this->insert_data_into_bd($report, $arDocument);
            $this->log(message: 'Документ загружен', type: CustomReportLogType::DEBUG, storing: false);

            $this->mark_report_as_worked($report);
        }
        $arDocument = null;
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
            $this->report_types = CustomReportType::query()
                ->select(['id', 'title'])
                ->get();
        }

        $report_type = $this->report_types->firstWhere("id", "=", $report_id);
        if (is_null($report_type)) {
            return false;
        } else {
            return $report_type->title;
        }
    }

    private function log(
        string $message,
        CustomReportLogType $type,
        bool $storing = true,
        ?CustomReport $customReport = null,
        ?CustomReportType $customReportType = null,
        ?User $user = null,
        ?string $filepath = null,
        ?string $templateFilepath = null,
    ): void {
        if ($this->console) {
            $comment = [
                "message: {$message}",
            ];

            if ($customReport) $comment[] = "customReport: {$customReport->id}";
            if ($customReportType) $comment[] = "customReportType: {$customReportType->title}";
            if ($user) $comment[] = "user: {@$user->id}";
            if ($filepath) $comment[] = "filepath: {@$filepath}";
            if ($templateFilepath) $comment[] = "template_filepath: {@$templateFilepath}";

            $type->print($this->console, $type->name() . ': [' . implode('; ', $comment) . ']');
        }

        if ($storing) {
            (new CustomReportLog())->fill([
                'message' => $message,
                'type' => $type->value,
                'custom_report_id' => @$customReport->id,
                'custom_report_type_id' => @$customReportType->id,
                'user_id' => @$user->id,
                'filepath' => @$filepath,
                'template_filepath' => @$templateFilepath,
            ])->save();
        }

        if (empty($user) == false && empty($customReportType) == false) {
            $telegramMessage = null;

            if ($type == CustomReportLogType::ACCESS) {
                $telegramMessage = "Ваш отчет \"{$customReportType->title}\" был успешно загружен ";
            } elseif ($type == CustomReportLogType::ERROR) {
                $telegramMessage = "Ваш отчет \"{$customReportType->title}\" не был загружен с ошибкой: $message";
            }

            if (empty($telegramMessage) == false) {
                try {
                    TelegramBot::notify($user, 'Загрузка отчета', $telegramMessage);
                } catch (Throwable | Exception) {
                    $this->log('ТГ пользователь не найден', CustomReportLogType::DEBUG, false);
                }
            }
        }
    }
}
