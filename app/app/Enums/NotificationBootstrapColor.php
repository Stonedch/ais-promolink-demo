<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * This class represents a list of colors.
 */
enum NotificationBootstrapColor: string
{
    case DANGER = 'danger';
    case PRIMARY = 'default';
    case DARK = 'dark';

    public static function find(string $query): self
    {
        return self::tryFrom($query) ?: self::PRIMARY;
    }

    public function title(): string
    {
        return match ($this) {
            self::DANGER => 'Критичные',
            self::PRIMARY => 'Информирование',
            self::DARK => 'Системные уведомления',
        };
    }

    public function bootstrapme(): string
    {
        return match ($this) {
            self::DANGER => 'bg-danger',
            self::PRIMARY => 'bg-light',
            self::DARK => 'bg-dark',
        };
    }

    public function bootstrapmeColor(): string
    {
        return match ($this) {
            self::DANGER => 'text-light',
            self::PRIMARY => 'text-dark',
            self::DARK => 'text-light',
        };
    }
}
