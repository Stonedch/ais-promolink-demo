<?php

namespace App\Enums;

enum FormStructureType: int
{
    case FIELD = 100;
    case GROUP = 200;

    public function code()
    {
        return match ($this) {
            static::FIELD => 'Поле',
            static::GROUP => 'Группа',
        };
    }

    public static function getSelectOptions() {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->code();
        }

        return $options;
    }
}
