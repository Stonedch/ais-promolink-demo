<?php

namespace App\Plugins\EntityLogger\Enums;

enum EntityLoggerMessage: int
{
    case SAVING = 100;
    case SOFT_DELETING = 200;
    case HARD_DELETING = 300;
    case RESTORING = 400;
    case DELETING = 500;

    public function message(): string
    {
        return match ($this) {
            self::SAVING => 'Saving',
            self::SOFT_DELETING => 'Soft deleting',
            self::HARD_DELETING => 'Hard deleting',
            self::RESTORING => 'Restoring',
            self::DELETING => 'Deleting',
        };
    }
}
