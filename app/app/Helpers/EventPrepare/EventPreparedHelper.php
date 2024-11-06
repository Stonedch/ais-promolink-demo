<?php

namespace App\Helpers\EventPrepare;

use App\Exceptions\EventPrepareException;
use App\Models\CollectionValue;
use App\Models\Departament;
use App\Models\Event;
use App\Models\FormResult;
use App\Models\PreparedEvent;
use App\Models\PreparedFormResult;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class EventPreparedHelper
{
    public static function prepare(
        Event $event,
        Collection $formResults = null,
        User $user = null,
        Departament $departament = null
    ): ?PreparedEvent {
        $preparedEvent = PreparedEvent::findByEventOrCreate($event);

        try {
            throw_if($event->exists == false, new EventPrepareException('Event not exists'));

            $formResults = empty($formResults) ? FormResult::where('event_id', $event->id)->get() : $formResults;

            $user = empty($user) ? User::find($event->user_id) : $user;
            throw_if(empty($user), new EventPrepareException('User of event not found'));

            $departament = empty($departament) ? Departament::find($user->departament_id) : $departament;
            throw_if(empty($departament), EventPrepareException::class);

            $structure = $event->getStructure();
            throw_if(empty($structure), new EventPrepareException('Structure of event not found'));

            $fields = $event->getStructureFields($structure);
            $groups = $event->getStructureGroups($structure);
            $blockeds = $event->getStructureBlockeds($structure);

            $preparedEvent->fill([
                'event_id' => $event->id,
                'user_fullname' => $user->getFullname(),
                'departament_name' => $departament->name,
                'form_name' => $structure->form->name,
                'event_created_at' => $event->created_at,
                'event_filled_at' => $event->filled_at,
                'event_refilled_at' => $event->refilled_at,
            ])->save();

            PreparedFormResult::deleteByPreapredEvent($preparedEvent);

            $formResults->map(function (FormResult $formResult) use ($preparedEvent, $fields, $groups, $blockeds) {
                $field = $fields[$formResult->field_id];
                $value = self::getValueOrOption($formResult, $field);
                $rowKeyStructure = self::getRowKeyStructure($fields, $blockeds, $field, $formResult);
                $groupKeyStructure = self::getGroupKeyStructure($groups, $field);

                (new PreparedFormResult())->fill([
                    'prepared_event_id' => $preparedEvent->id,
                    'field_id' => $formResult->field_id,
                    'row_key_structure' => $rowKeyStructure->imploded,
                    'row_key_first' => $rowKeyStructure->first,
                    'group_key_structure' => $groupKeyStructure->imploded,
                    'key' => $field->name,
                    'value' => $value,
                    'index' => $formResult->index,
                ])->save();
            });

            $event->prepared_at = now();
            $event->save();

            Log::channel('event-prepare')->info("[event: {$event->id}] prepared");
        } catch (Throwable | Exception $e) {
            Log::channel('event-prepare')->error("[event: {$event->id}] {$e->getMessage}");
        }

        return $preparedEvent;
    }

    protected static function getValueOrOption(FormResult $formResult, object $field): string
    {
        $value = null;

        if (empty($field->collection_id) == false) {
            $option = CollectionValue::query()
                ->where('id', $value)
                ->first();

            if (empty($option) == false) {
                $value = $option->value;
            }
        }

        return $value ?: $formResult->value;
    }

    protected static function getGroupKeyStructure(object $groups, object $field): object
    {
        $structure = (object) [
            'structure' => null,
            'imploded' => null,
        ];

        if (empty($field->group_id) == false) {
            $parentGroup = $field->group_id;

            while (true) {
                try {
                    $parentGroup = $groups->get($parentGroup);

                    throw_if(empty($parentGroup));

                    if (is_null($structure->structure)) {
                        $structure->structure = [];
                    }

                    $structure->structure[] = $parentGroup->name;
                    $parentGroup = $parentGroup->parent_id;
                } catch (Throwable | Exception) {
                    break;
                }
            }
        }

        $structure->imploded = implode('; ', array_reverse($structure->structure ?: []));

        return $structure;
    }

    protected static function getRowKeyStructure(object $fields, object $blockeds, object $field, FormResult $formResult): object
    {
        $structure = (object) [
            'left' => $fields->where('sort', '<', $field->sort),
            'isBlocked' => empty($blockeds->where('field_id', $field->id)->where('index', $formResult->index)->count()) == false,
            'structure' => null,
            'imploded' => null,
            'first' => null,
        ];

        if ($structure->isBlocked) {
            return $structure;
        }

        if ($structure->left->count()) {
            $findedBlockeds = $blockeds->whereIn('field_id', $structure->left->pluck('id'))->where('index', $formResult->index);
            $structure->first = $findedBlockeds->pluck('value')->first();
            $structure->structure = $findedBlockeds->pluck('value');
            $structure->imploded = implode('; ', $structure->structure->toArray());
        }

        return $structure;
    }
}
