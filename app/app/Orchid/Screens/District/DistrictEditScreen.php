<?php

declare(strict_types=1);

namespace App\Orchid\Screens\District;

use App\Models\District;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class DistrictEditScreen extends Screen
{
    public $district;

    public function query(District $district): iterable
    {
        return [
            'district' => $district,
        ];
    }

    public function name(): ?string
    {
        return 'Управление районами';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.districts.edit',
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
                ->canSee($this->district->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('district.name')
                    ->require()
                    ->title('Название'),
            ]),
        ];
    }

    public function save(Request $request, District $district)
    {
        $district->fill($request->input('district', []));
        $district->save();
        Toast::info('Успешно сохранено!');
        return redirect()->route('platform.districts.edit', $district);
    }

    public function remove(District $district)
    {
        $district->delete();
        Toast::info('Успешно удалено');
        return redirect()->route('platform.districts');
    }
}
