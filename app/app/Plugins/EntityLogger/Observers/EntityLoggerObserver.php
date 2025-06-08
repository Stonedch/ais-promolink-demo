<?php

declare(strict_types=1);

namespace App\Plugins\EntityLogger\Observers;

use App\Plugins\EntityLogger\Enums\EntityLoggerMessage;
use App\Plugins\EntityLogger\Providers\EntityLoggerServiceProvider;
use App\Plugins\EntityLogger\Services\LoggerService;
use Illuminate\Database\Eloquent\Model;

class EntityLoggerObserver
{
    public function created(Model $entity)
    {
        if (EntityLoggerServiceProvider::isActive()) {
            LoggerService::info(EntityLoggerMessage::SAVING, $entity);
        }
    }

    public function saving(Model $entity)
    {
        if (EntityLoggerServiceProvider::isActive()) {
            LoggerService::info(EntityLoggerMessage::SAVING, $entity);
        }
    }

    public function deleting(Model $entity)
    {
        if (EntityLoggerServiceProvider::isActive()) {
            LoggerService::info(EntityLoggerMessage::DELETING, $entity);
        }
    }
}
