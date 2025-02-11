<?php

declare(strict_types=1);

namespace App\Console\Commands\Seeding;

use Illuminate\Console\Command;
use Orchid\Platform\Models\Role;
use Orchid\Support\Facades\Dashboard;

class RolesCommand extends Command
{
    protected $name = 'seeding:roles';
    protected $signature = 'seeding:roles';
    protected $description = 'Создание ролей';

    public function handle(): void
    {
        self::createRole('Сотрудник учреждения', 'departament-worker', [
            'platform.systems.attachment' => 1,
        ]);

        self::createRole('Сотрудник учреждения (кастомные отчеты)', 'departament-worker-cr', [
            'platform.custom-reports.loading' => 1,
            'platform.systems.attachment' => 1,
        ]);

        self::createRole('Директор учреждения', 'departament-director', [
            'platform.departament-director.base' => 1,
            'platform.systems.attachment' => 1,
        ]);

        self::createRole('Министр', 'min', [
            'platform.min.base' => 1,
            'platform.systems.attachment' => 1,
        ]);

        self::createRole('Проверяющий', 'checker', [
            'platform.checker.base' => 1,
            'platform.systems.attachment' => 1,
        ]);

        self::createRole('Руководитель', 'supervisor', [
            'platform.supervisor.base' => 1,
            'platform.systems.attachment' => 1
        ]);

        self::createRole('Админ', 'admin', Dashboard::getAllowAllPermission());
    }

    protected static function createRole(
        string $name,
        string $slug,
        iterable $permissions
    ): ?Role {
        $role = new Role();

        if (empty(Role::where('slug', $slug)->count())) {
            $role->fill([
                'name' => $name,
                'slug' => $slug,
                'permissions' => $permissions,
            ])->save();
        }

        return $role->exists ? $role : null;
    }
}
