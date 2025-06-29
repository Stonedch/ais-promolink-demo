<?php

declare(strict_types=1);

namespace App\Console\Commands\Plugins\AccountingVulnerabilities;

use App\Plugins\AccountingVulnerabilities\Models\Vulnerability;
use App\Plugins\AccountingVulnerabilities\Models\VulnerabilitySoft;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class SafeSurfParseCommand extends Command
{
    protected $signature = 'plugins:accounting-vulnerabilities:parse:safe-surf';

    public function handle(): void
    {
        // Создаём клиент Guzzle с базовым URL
        $client = new Client([
            'base_uri' => 'https://safe-surf.ru',
            'timeout'  => 60.0,
            // 'verify' => false, // при необходимости отключить проверку SSL
        ]);

        $page = 1;
        $hasMore = true;

        // Цикл по страницам
        while ($hasMore) {
            // Пример структуры URL с параметром пагинации PAGEN_1
            $url = "/specialists/bulletins-nkcki/?PAGEN_1={$page}";

            $this->info("Обработка страницы: {$page}");

            try {
                $response = $client->get($url, ['verify' => false]);
                $html = (string)$response->getBody();
            } catch (\Throwable $e) {
                $this->error("Ошибка при загрузке страницы {$url}: " . $e->getMessage());
                break;
            }

            // Парсим HTML
            $crawler = new Crawler($html);

            // Ищем строки с уязвимостями / бюллетенями
            // (селекторы зависят от реального HTML на сайте)
            $rows = $crawler->filter('div.row-bulletin-nkcki');

            // Если строк не нашлось, завершаем
            if ($rows->count() === 0) {
                $this->info("Записи отсутствуют, завершаем парсинг.");
                $hasMore = false;
                break;
            }

            // Обрабатываем каждую запись
            $rows->each(function (Crawler $node) use ($client) {
                try {
                    // Пример: вытаскиваем ссылку на детальное описание
                    // Класс и структура зависят от реального HTML
                    $detailLinkNode = $node->filter('.link-icon:nth-child(3)');
                    if ($detailLinkNode->count() === 0) {
                        $this->warn("Ссылка на детальное описание не найдена.");
                        return;
                    }

                    $detailLink = $detailLinkNode->attr('href');
                    if (!$detailLink) {
                        $this->warn("Пустая ссылка на деталь.");
                        return;
                    }

                    // Формируем полный URL
                    if (!str_contains($detailLink, 'http')) {
                        $detailLink = 'https://safe-surf.ru' . $detailLink;
                    }

                    // Пример: извлекаем дату бюллетеня
                    // (смотрим нужную ячейку, здесь: .cell-bulletin-nkcki.cell-1 .cell-value)
                    $dateTextNode = $node->filter('.cell-bulletin-nkcki.cell-1 .cell-value');
                    $dateText = $dateTextNode->count() ? $dateTextNode->text() : null;

                    if (!$dateText) {
                        $this->warn("Не найдена дата бюллетеня.");
                        return;
                    }

                    // Преобразуем дату в \DateTime (формат может быть иной, чем d.m.Y)
                    $date = \DateTime::createFromFormat('d.m.Y', $dateText);
                    if (!$date) {
                        $this->warn("Не удалось распознать дату: {$dateText}");
                        return;
                    }

                    // Пример: фильтр по дате, если нужно
                    // if ($date->getTimestamp() < now()->subDays(7)->startOfDay()->getTimestamp()) {
                    //     return;
                    // }

                    // Загружаем детальную страницу
                    $detailHtml = (string)$client->get($detailLink, ['verify' => false])->getBody();
                    $detailCrawler = new Crawler($detailHtml);

                    // Предположим, на детальной странице есть JSON, как в исходном коде:
                    // Паттерн может отличаться, нужно смотреть реальную структуру.
                    if (preg_match('/const bulletins = reactive$(\{.*?\})$;/s', $detailHtml, $matches)) {
                        $json = $matches[1];
                        $data = json_decode($json, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new \RuntimeException("Ошибка JSON: " . json_last_error_msg());
                        }

                        // Извлечение уникального идентификатора (CVE или иной)
                        $cve = null;
                        if (!empty($data['vulnerability_ids'])) {
                            // Предположим, что в массиве vulnerability_ids
                            // может быть CVE-xxxx-xxxxxx
                            foreach ((array)$data['vulnerability_ids'] as $idval) {
                                if (preg_match('/CVE-\d{4}-\d+/', $idval, $m)) {
                                    $cve = $m[0];
                                    break;
                                }
                            }
                        }

                        // Проверяем уникальность
                        if ($cve) {
                            $existingVulnerability = Vulnerability::where('cve', $cve)->first();
                            if ($existingVulnerability) {
                                $this->info("Уязвимость уже есть в базе: {$cve}");
                                return;
                            }
                        }

                        // Вектор атаки
                        $vector = $data['attack_vector'] ?? null;
                        if ($vector) {
                            // убираем HTML-теги и пробелы
                            $vector = strip_tags(trim($vector));
                        }

                        // Из названия и описания
                        $vulName = $data['title'] ?? 'Не указано';
                        $vulDesc = $data['description'] ?? 'Не указано';

                        // Уровень опасности (grade)
                        $vulCritu = $data['criticality'] ?? null;
                        if ($vulCritu) {
                            $vulCritu = trim(preg_replace('/\s+/', ' ', strip_tags($vulCritu)));
                        }

                        // Рекомендации по устранению
                        $vulElimination = $data['update_available'] ?? null;

                        // Сохраняем запись в таблицу Vulnerability
                        $vulnerability = Vulnerability::create([
                            'name' => $vulName,
                            'description' => $vulDesc,
                            'bdu' => $data['bulletin_id'] ?? null, // или любой другой ID
                            'cve' => $cve,
                            'vector' => $vector,
                            'grade' => $vulCritu,
                            'elimination' => $vulElimination,
                        ]);

                        // Сохраняем связанное ПО (если есть)
                        // Предположим, в json: "products" => ["Продукт1", "Продукт2"]
                        $softs = $data['products'] ?? [];
                        if (!is_array($softs)) {
                            $softs = [$softs];
                        }

                        foreach ($softs as $softName) {
                            if (!empty($softName)) {
                                VulnerabilitySoft::create([
                                    'name' => strip_tags($softName),
                                    'vulnerability_id' => $vulnerability->id,
                                ]);
                            }
                        }

                        $this->info("Сохранена уязвимость: {$vulName} (CVE: {$cve})");
                    } else {
                        $this->warn("JSON не найден на детальной странице: {$detailLink}");
                    }
                } catch (\Throwable $e) {
                    $this->error("Ошибка при обработке: " . $e->getMessage());
                }
            });

            $page++;
            // Если нужно ограничить число страниц или
            // использовать логику "hasMore", её можно доработать.
            if ($page > 10) {
                // Пример искусственного лимита в 10 страниц
                $this->info("Достигнут лимит страниц. Остановка.");
                break;
            }
        }

        $this->info("Парсинг завершён на странице: {$page}");
    }
}