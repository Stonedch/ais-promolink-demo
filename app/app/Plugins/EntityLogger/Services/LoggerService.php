<?php

namespace App\Plugins\EntityLogger\Services;

use App\Plugins\EntityLogger\Enums\EntityLoggerMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LoggerService
{
    protected const CHANNEL = 'entities';

    public static function info(
        EntityLoggerMessage $entityLoggerMessage,
        Model $entity
    ): void {
        Log::channel(self::CHANNEL)->info(
            $entityLoggerMessage->message(),
            [
                'message' => $entityLoggerMessage->value,
                'model' => $entity::class,
                'fields' => $entity->toArray(),
                'user' => @request()->user() ? ['id' => request()->user()->id] : null,
                'ip' => @request()->ip(),
                'datetime' => now(),
            ]
        );
    }

    public static function error(
        EntityLoggerMessage $entityLoggerMessage,
        Model $entity
    ): void {
        Log::channel(self::CHANNEL)->error(
            $entityLoggerMessage->message(),
            $entity->toArray()
        );
    }
}
