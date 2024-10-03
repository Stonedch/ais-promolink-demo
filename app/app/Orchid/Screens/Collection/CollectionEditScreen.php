<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Collection;

use App\Models\Collection;
use App\Models\CollectionValue;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CollectionEditScreen extends Screen
{
    public $collection;

    public function query(Collection $collection): iterable
    {
        return [
            'collection' => $collection,
            'collection_values' => $collection->values()->orderBy('sort')->get(),
        ];
    }

    public function name(): ?string
    {
        return 'Управление коллекцией';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.collections.edit',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('bs.check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('bs.trash')
                ->method('remove')
                ->canSee($this->collection->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('collection.name')
                    ->require()
                    ->title('Название'),
            ]),

            Layout::rows([
                Matrix::make('collection_values')
                    ->columns([
                        '#' => 'id',
                        'Значение' => 'value',
                        'Сортировка' => 'sort',
                    ])
                    ->fields([
                        'id' => Input::make()->hidden(),
                        'value' => Input::make()->require(),
                        'sort' => Input::make()->type('number')->class("form-control _sortable"),
                    ])
                    ->title('Значения'),
            ]),
        ];
    }

    public function save(Request $request, Collection $collection)
    {
        $collection->fill($request->input('collection', []));
        $collection->save();

        $workedValueIdentifiers = [];

        if (empty($request->input('collection_values', [])) == false) {
            foreach ($request->input('collection_values') as $row) {
                if (empty($row['value'])) continue;

                $value = empty($row['id']) == false
                    ? CollectionValue::find($row['id'])
                    : new CollectionValue();

                $value->fill([
                    'value' => $row['value'],
                    'collection_id' => $collection->id,
                    'sort' => $row['sort'],
                ])->save();

                $workedValueIdentifiers[] = $value->id;
            }
        }

        $collection->values()->whereNotIn('id', $workedValueIdentifiers)->delete();

        Toast::info('Успешно сохранено!');
        return redirect()->route('platform.collections.edit', $collection);
    }

    public function remove(Collection $collection)
    {
        $collection->delete();
        Toast::info('Успешно удалено');
        return redirect()->route('platform.collections');
    }
}
