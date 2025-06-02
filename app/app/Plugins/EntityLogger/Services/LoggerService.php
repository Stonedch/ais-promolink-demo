<?php

namespace App\Plugins\EntityLogger\Services;

use App\Plugins\EntityLogger\Enums\EntityLoggerMessage;
use App\Plugins\EntityLogger\Models\EntityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LoggerService
{
    protected const CHANNEL = 'entities';

    public static function info(
        EntityLoggerMessage $entityLoggerMessage,
        Model $entity
    ): void {
        $data =  [
            'message' => $entityLoggerMessage->value,
            'model' => $entity::class,
            'fields' => $entity->toArray(),
            'user' => @request()->user() ? ['id' => request()->user()->id] : null,
            'ip' => @request()->ip(),
            'datetime' => now(),
        ];

        Log::channel(self::CHANNEL)->info($entityLoggerMessage->message(), $data);

        $data['fields'] = json_encode($data['fields'], JSON_UNESCAPED_UNICODE);
        $data['user'] = json_encode($data['user'], JSON_UNESCAPED_UNICODE);

        EntityLog::create($data);
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
