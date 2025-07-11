<?php

namespace App\Orchid\Layouts\User;

use App\Orchid\Filters\HasAvatarRole;
use App\Orchid\Filters\HasTGFilter;
use App\Orchid\Filters\RoleFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class UserFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            RoleFilter::class,
            HasAvatarRole::class,
            HasTGFilter::class,
        ];
    }
}
