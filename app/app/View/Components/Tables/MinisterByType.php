<?php

namespace App\View\Components\Tables;

use App\Models\Departament;
use App\Models\DepartamentType;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class MinisterByType extends Component
{
    protected const VIEW = 'components.tables.minister-by-type';

    protected array $data = [];

    public function __construct(Collection $types)
    {
        $departaments = Departament::all();

        $types->map(function (DepartamentType $type) use ($departaments) {
            $type->count = $departaments
                ->where('departament_type_id', $type->id)
                ->pluck('district_id')
                ->unique()
                ->count();

            return $type;
        });

        $this->data['types'] = $types->sortBy('sort');
    }

    public function render(): View|Closure|string
    {
        return view(self::VIEW, $this->data);
    }
}
