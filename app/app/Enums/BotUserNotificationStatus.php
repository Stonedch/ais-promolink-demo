<?php

namespace App\Enums;

enum BotUserNotificationStatus: int
{
    case IN_PROGRESS = 100;
    case ERROR = 200;
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
            static::ERROR => 'Ошибка',
            static::READY => 'Выполнен',
        };
    }

    public function bootstrapme(): string
    {
        return match ($this) {
            static::IN_PROGRESS => '<span class="badge rounded-pill bg-primary">' . $this->name() . '</span>',
            static::ERROR => '<span class="badge rounded-pill bg-danger">' . $this->name() . '</span>',
            static::READY => '<span class="badge rounded-pill bg-success">' . $this->name() . '</span>',
        };
    }
}
