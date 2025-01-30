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

    public function bootstrapme(): string
    {
        return match ($this) {
            self::DANGER => 'bg-danger',
            self::PRIMARY => 'bg-primary',
            self::DARK => 'bg-dark',
        };
    }
}
