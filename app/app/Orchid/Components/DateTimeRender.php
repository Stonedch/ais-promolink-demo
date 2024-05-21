<?php

namespace App\Orchid\Components;

use Illuminate\Support\Carbon;
use Illuminate\View\Component;

class DateTimeRender extends Component
{
    public function __construct(
        protected mixed $value,
        protected string $upperFormat = 'M j, Y',
        protected string $lowerFormat = 'D, h:i'
    ) {
    }

    public function render()
    {
        $this->value = date('d.m.Y H:i:s', strtotime($this->value));
        $date = Carbon::parse($this->value);

        return sprintf(
            '<time class="mb-0 text-capitalize">%s<span class="text-muted d-block">%s</span></time>',
            $date->translatedFormat($this->upperFormat),
            $date->translatedFormat($this->lowerFormat),
        );
    }
}
