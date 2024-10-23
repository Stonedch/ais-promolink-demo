<?php

declare(strict_types=1);

namespace App\Orchid\Fields;

use Orchid\Screen\Actions\Button as ParentButton;

class Button extends ParentButton
{
    protected $view = 'platform.actions.button';

    public function data(array $data): Button
    {
        $this->attributes['data'] = $data;

        return $this;
    }
}
