<?php

declare(strict_types=1);

namespace App\Orchid\Fields;

use Orchid\Screen\Fields\Matrix;

class FormItemMatrix extends Matrix
{
    protected $view = 'platform.fields.matrix';

    public function hiddenColumns(array $columns) {
        $this->attributes['hiddenColumns'] = $columns;
        return $this;
    }

    public function withHiddenColumns() {
        $this->attributes['withHiddenColumns'] = true;
        return $this;
    }
}
