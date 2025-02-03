<?php

namespace App\View\Components\Tables;

use App\Models\Departament;
use App\Models\District;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class MinisterByDepartaments extends Component
{
    protected const VIEW = 'components.tables.minister-by-departaments';

    protected array $departaments = [];

    public function __construct(Collection $departaments, District $district)
    {
        $departaments->map(function (Departament $departament) {
            $departament->rating = $departament->rating ?: 0;
            return $departament;
        });

        $this->data['departaments'] = $departaments;
        $this->data['district'] = $district;
    }

    public function render(): View|Closure|string
    {
        return view(self::VIEW, $this->data);
    }
}
