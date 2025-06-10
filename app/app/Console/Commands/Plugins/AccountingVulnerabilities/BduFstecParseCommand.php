<?php

declare(strict_types=1);

namespace App\Console\Commands\Plugins\AccountingVulnerabilities;

use App\Plugins\AccountingVulnerabilities\Models\Vulnerability;
use App\Plugins\AccountingVulnerabilities\Models\VulnerabilitySoft;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class BduFstecParseCommand extends Command
{
    protected $signature = 'plugins:accounting-vulnerabilities:parse:bdu-fstec';

    public function handle(): void
    {
        $client = new Client([
            'base_uri' => 'https://bdu.fstec.ru',
            'timeout'  => 60.0,
        ]);

        $page = 1;
        $hasMore = true;

        while ($hasMore) {
            $response = $client->get("/vul?page={$page}&size=100", ['verify' => false]);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            $rows = $crawler->filter('table.table-striped.table-vuls tr');

            if ($rows->count() === 0) {
                $hasMore = false;
                break;
            }

            $rows->each(function (Crawler $node) use ($client) {
                try {
                    $link = $node->filter('td')->eq(0)->filter('a')->attr('href');
                    $dateText = $node->filter('td')->eq(2)->filter('span')->last()->text();

                    $date = \DateTime::createFromFormat('d.m.Y', $dateText);
                    if (!$date) {
                        $this->warn("Не удалось распознать дату: {$dateText}");
                        return;
                    }

                    if ($date->getTimestamp() < now()->subDays(7)->startOfDay()->getTimestamp()) {
                        return;
                    }

                    $html = (string) $client->get($link, ['verify' => false])->getBody();

                    if (preg_match('/const v_model = reactive\((\{.*?\})\);/s', $html, $matches)) {
                        $json = $matches[1];
                        $data = json_decode($json, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new \RuntimeException("Ошибка JSON: " . json_last_error_msg());
                        }

                        // Извлекаем CVE
                        $cve = null;
                        if (!empty($data['idvals'])) {
                            foreach ((array)$data['idvals'] as $idval) {
                                if (preg_match('/CVE-\d{4}-\d+/', $idval, $matches)) {
                                    $cve = $matches[0];
                                    break;
                                }
                            }
                        }

                        // Проверяем, существует ли уже такая уязвимость
                        $existingVulnerability = Vulnerability::where('cve', $cve)->first();
                        if ($existingVulnerability) {
                            $this->info("Уязвимость уже существует: {$cve}");
                            return;
                        }

                        // Обрабатываем CVSS вектор
                        $vector = null;
                        if (!empty($data['cvsses3'][0])) {
                            $vector = strip_tags($data['cvsses3'][0]);
                        } elseif (!empty($data['cvsses'][0])) {
                            $vector = strip_tags($data['cvsses'][0]);
                        }

                        // Очищаем и форматируем данные
                        $vulName = $data['vul_name'] ?? 'Не указано';
                        $vulDesc = $data['vul_desc'] ?? 'Не указано';
                        $vulCritu = !empty($data['vul_critu']) ? trim(preg_replace('/\s+/', ' ', strip_tags($data['vul_critu']))) : null;
                        $vulElimination = $data['vul_elimination'] ?? null;

                        // Создаём уязвимость
                        $vulnerability = Vulnerability::create([
                            'name' => $vulName,
                            'description' => $vulDesc,
                            'bdu' => !empty($data['idvals'][0]) ? "BDU-ID: " . strip_tags($data['idvals'][0]) : null,
                            'cve' => $cve,
                            'vector' => $vector,
                            'grade' => $vulCritu,
                            'elimination' => $vulElimination,
                        ]);

                        // Обрабатываем связанное ПО
                        $softs = $data['softs'] ?? [];
                        if (!is_array($softs)) {
                            $softs = [$softs];
                        }

                        foreach ($softs as $softName) {
                            if (!empty($softName)) {
                                VulnerabilitySoft::create([
                                    'name' => $softName,
                                    'vulnerability_id' => $vulnerability->id,
                                ]);
                            }
                        }

                        $this->info("Сохранено: {$vulName} (CVE: {$cve})");
                    } else {
                        $this->warn("Не найден JSON на странице: {$link}");
                    }
                } catch (\Throwable $e) {
                    $this->error("Ошибка при обработке {$link}: " . $e->getMessage());
                }
            });

            $page++;
        }

        $this->info("✅ Парсинг завершён. Обработано страниц: " . ($page - 1));
    }
}
