<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class HasAvatarRole extends Filter
{
    public function name(): string
    {
        return 'Установлен аватар?';
    }

    public function parameters(): array
    {
        return ['has-avatar'];
    }

    public function run(Builder $builder): Builder
    {
        return $builder->where(
            'attachment_id',
            $this->request->get('has-avatar') ? '!=' : '=',
            null
        );
    }

    public function display(): array
    {
        return [
            Select::make('has-avatar')
                ->empty()
                ->options([
                    0 => 'Нет',
                    1 => 'Да',
                ])
                ->value($this->request->get('has-avatar'))
                ->title('Установлен аватар?'),
        ];
    }

    public function value(): string
    {
        return $this->name() . ': ' . ($this->request->get('has-avatar') ? 'Да' : 'Нет');
    }
}
