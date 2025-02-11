<?php

namespace App\View\Components\Accordion;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Item extends Component
{
    static protected int $count = 0;

    protected const VIEW = 'components.accordion.item';
    protected iterable $body = [];

    public function __construct(string $title, bool $show = false)
    {
        $this->body['id'] = md5($title . ++self::$count);
        $this->body['title'] = $title;
        $this->body['show'] = $show;
    }

    public function render(): View|Closure|string
    {
        return view(self::VIEW, $this->body);
    }
}
