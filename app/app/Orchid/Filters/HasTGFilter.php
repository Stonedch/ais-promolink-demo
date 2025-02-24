<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use App\Models\BotUser;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class HasTGFilter extends Filter
{
    public function name(): string
    {
        return 'Установлен ТГ?';
    }

    public function parameters(): array
    {
        return ['has-tg'];
    }

    public function run(Builder $builder): Builder
    {
        // dd(BotUser::where('user_id', 232)->first());
        return $this->request->get('has-tg')
            ? $builder->whereIn('phone', BotUser::select('phone'))
            : $builder->whereNotIn('phone', BotUser::select('phone'));
    }

    public function display(): array
    {
        return [
            Select::make('has-tg')
                ->empty()
                ->options([
                    0 => 'Нет',
                    1 => 'Да',
                ])
                ->value($this->request->get('has-tg'))
                ->title('Установлен ТГ?'),
        ];
    }

    public function value(): string
    {
        return $this->name() . ': ' . ($this->request->get('has-tg') ? 'Да' : 'Нет');
    }
}
