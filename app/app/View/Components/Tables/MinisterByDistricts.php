<?php

namespace App\View\Components\Tables;

use App\Models\Departament;
use App\Models\District;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class MinisterByDistricts extends Component
{
    protected const VIEW = 'components.tables.minister-by-districts';
    protected array $data = [];

    public function __construct(Collection $districts)
    {
        $departaments = Departament::all();

        $districts->map(function (District $district) use ($departaments) {
            $district->count = $departaments->where('district_id', $district->id)->count();
            $district->letter = mb_substr($district->name, 0, 1);
            return $district;
        });

        $this->data['districts'] = $districts->groupBy('letter')->sort();
    }

    public function render(): View|Closure|string
    {
        return view(self::VIEW, $this->data);
    }
}
