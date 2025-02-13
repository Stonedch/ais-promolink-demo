<?php

namespace App\Enums;

use Symfony\Component\Console\Command\Command;

enum CustomReportLogType: int
{
    case LOG = 100;
    case DEBUG = 200;
    case WARNING = 300;
    case ERROR = 400;
    case ACCESS = 500;

    public static function options(array $options = []): array
    {
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->name();
        }

        return $options;
    }

    public function name(): string
    {
        return match ($this) {
            static::LOG => 'Лог',
            static::DEBUG => 'Тестирование',
            static::WARNING => 'Предупреждение',
            static::ERROR => 'Ошибка',
            static::ACCESS => 'Успешно',
        };
    }

    public function print(Command $console, string $message): void
    {
        match ($this) {
            static::LOG => $console->line($message),
            static::DEBUG => $console->line($message),
            static::WARNING => $console->warn($message),
            static::ERROR => $console->error($message),
            static::ACCESS => $console->info($message)
        };
    }
}
