<?php

namespace App\View\Components\Tables;

use App\Models\Event;
use App\Models\Form;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\View\Component;

class BaseForms extends Component
{
    protected const VIEW = 'components.tables.base-forms';

    protected array $data = [];

    public function __construct(
        SupportCollection $forms,
        bool $checking = true
    ) {
        $this->data['user'] = request()->user();
        $this->data['forms'] = $forms;

        if ($checking) {
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

        $this->data['forms'] = $this->data['forms']->whereNotNull('event')->sortBy('sort');
    }

    public function render(): View|Closure|string
    {
        return view(self::VIEW, $this->data);
    }
}
