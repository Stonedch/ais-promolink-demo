<?php

namespace App\Enums;

enum EventStatus: int
{
    case IN_PROGRESS = 100;
    case EXPIRED = 200;
    case READY = 300;

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
            static::IN_PROGRESS => 'В процессе',
            static::EXPIRED => 'Просрочен',
            static::READY => 'Выполнен',
        };
    }

    public function bootstrapme(): string
    {
        return match ($this) {
            static::IN_PROGRESS => '<span class="badge rounded-pill bg-primary">' . $this->name() . '</span>',
            static::EXPIRED => '<span class="badge rounded-pill bg-danger">' . $this->name() . '</span>',
            static::READY => '<span class="badge rounded-pill bg-success">' . $this->name() . '</span>',
        };
    }
}
