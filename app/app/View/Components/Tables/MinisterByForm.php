<?php

namespace App\View\Components\Tables;

use App\Models\Event;
use App\Models\Form;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class MinisterByForm extends Component
{
    protected const VIEW = 'components.tables.minister-by-form';

    protected iterable $data = [];

    public function __construct(Collection $forms)
    {
        $this->data['user'] = request()->user();
        $this->data['forms'] = $forms->sortBy('sort');

        $this->data['forms'] = $this->data['forms']->map(function (array|Form $form) {
            if (is_array($form)) {
                $data = $form['id'];
                $form = new Form($form);
                $form->id = $data;
            }

            $form->event = Event::lastByDepartament($form->id, $this->data['user']->departament_id);
            return $form;
        });
    }

    public function render(): View|Closure|string
    {
        return view(self::VIEW, $this->data);
    }
}
