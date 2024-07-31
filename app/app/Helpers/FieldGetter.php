<?php

namespace App\Helpers;

use App\Models\Departament;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormResult;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use ZipArchive;

class FieldGetter
{
    // departament
    // fields

    public static function find(Departament $departament, array $forms, array $fields)
    {
        $values = [];

        try {
            $fieldIdentifiers = [];

            $events = Event::query()
                ->where('departament_id', $departament->id)
                ->whereIn('form_id', $forms)
                ->whereNotNull('filled_at')
                ->get()
                ->filter(function (Event $event) use ($fields) {
                    $suitable = false;

                    foreach ($fields as $field) {
                        $suitable = strpos($event->form_structure, $field) !== false;
                        if ($suitable) break;
                    }

                    return $suitable;
                })
                ->map(function (Event $event) use (&$fieldIdentifiers, $fields) {
                    $event->form_structure = json_decode($event->form_structure);

                    foreach ($event->form_structure->fields as $field) {
                        if (in_array($field->name, $fields)) {
                            $fieldIdentifiers[$field->id] = $field->name;
                        }
                    }

                    return $event;
                });

            $results = FormResult::query()
                ->whereIn('event_id', $events->pluck('id'))
                ->whereIn('field_id', array_keys($fieldIdentifiers))
                ->get()
                ->map(function (FormResult $formResult) use (&$values, $fieldIdentifiers) {
                    $values[$fieldIdentifiers[$formResult->field_id]] = $formResult->value;
                });
        } catch (Throwable) {
        }

        return $values;
    }
}
