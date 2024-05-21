<?php

namespace App\Orchid\Components;

use App\Helpers\PhoneNormalizer;
use Illuminate\View\Component;

class HumanizePhone extends Component
{
    public function __construct(
        protected mixed $value,
    ) {
    }

    public function render()
    {
        return PhoneNormalizer::humanizePhone($this->value);
    }
}
