<?php

declare(strict_types=1);

namespace App\Orchid\Screens\District;

use App\Models\District;
use App\Models\DistrictDashboardParam;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

class DistrictEditScreen extends Screen
{
    public $district;

    public function query(District $district): iterable
    {
        return [
            'district' => $district,
            'dashboard_params' => $district->exists
                ? DistrictDashboardParam::where('district_id', $district->id)->get()
                : new Collection(),
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

            Layout::rows([
                Matrix::make('dashboard_params')
                    ->columns([
                        '#' => 'id',
                        'Заголовок' => 'name',
                        'Значение' => 'value',
                        'Сортировка' => 'sort',
                    ])
                    ->fields([
                        'id' => Input::make()->disabled()->hidden(),
                        'name' => Input::make(),
                        'value' => Input::make(),
                        'sort' => Input::make()->type('number')->class("form-control _sortable"),
                    ])
                    ->title('Значения'),
            ])->title('Статистика')->canSee($this->district->exists),
        ];
    }

    public function save(Request $request, District $district)
    {
        $district->fill($request->input('district', []));
        $district->save();

        $dashboardParams = DistrictDashboardParam::where('district_id', $district->id)->get();
        $requestedDashboardParams = collect($request->input('dashboard_params', []));
        $isDashboardParamsReinit = true;

        if ($isDashboardParamsReinit) {
            $dashboardParams->map(fn (DistrictDashboardParam $param) => $param->delete());

            foreach ($requestedDashboardParams as $item) {
                try {
                    $param = new DistrictDashboardParam();
                    $param->fill($item);
                    $param->district_id = $district->id;
                    $param->sort = $param->sort ?: 100;
                    $param->save();
                } catch (Throwable) {
                    continue;
                }
            }
        }

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
