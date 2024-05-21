<?php

declare(strict_types=1);

namespace App\Orchid\Screens\DepartamentType;

use App\Models\Departament;
use App\Models\DepartamentType;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class DepartamentTypeEditScreen extends Screen
{
    public $departamentType;
    public $departaments;

    public function query(DepartamentType $departamentType): iterable
    {
        return [
            'departamentType' => $departamentType,
            'departaments' => Departament::query()
                ->where('departament_type_id', $departamentType->id)
                ->filters()
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Управление типом ведомств';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.departament-types.edit',
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
                ->canSee($this->departamentType->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('departamentType.name')
                    ->require()
                    ->title('Название'),
            ]),

            Layout::table('departaments', [
                TD::make('id', '#')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort()
                    ->width(100),

                TD::make('name', 'Название')
                    ->filter(TD::FILTER_TEXT)
                    ->sort()
                    ->width(200),
            ])->title('Ведомства'),
        ];
    }

    public function save(Request $request, DepartamentType $departamentType)
    {
        $departamentType->fill($request->input('departamentType', []));
        $departamentType->save();
        Toast::info('Успешно сохранено!');
        return redirect()->route('platform.departament-types.edit', $departamentType);
    }

    public function remove(DepartamentType $departamentType)
    {
        $departamentType->delete();
        Toast::info('Успешно удалено');
        return redirect()->route('platform.departament-types');
    }
}
