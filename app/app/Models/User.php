<?php

namespace App\Models;

use App\Helpers\PhoneNormalizer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;
use Orchid\Support\Facades\Dashboard;
use Throwable;

class User extends Authenticatable
{
    protected $fillable = [
        'phone',
        'departament_id',
        'email',
        'last_name',
        'first_name',
        'middle_name',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
        'email_verified_at' => 'datetime',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'departament_id' => Where::class,
        'phone' => Ilike::class,
        'email' => Ilike::class,
        'last_name' => Ilike::class,
        'first_name' => Ilike::class,
        'middle_name' => Ilike::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'phone',
        'departament_id',
        'email',
        'last_name',
        'first_name',
        'middle_name',
        'updated_at',
        'created_at',
    ];

    public static function createAdminByPhone(string $phone, string $password)
    {
        $normalized = PhoneNormalizer::normalizePhone($phone);

        throw_if(empty($normalized), 'Phone not normalized');
        throw_if(static::where('phone', $normalized)->exists(), 'User exist');

        $user = new User();

        $user->phone = $normalized;
        $user->password = Hash::make($password);
        $user->permissions = Dashboard::getAllowAllPermission();

        $user->save();
    }

    public function getDepartamentName(): ?string
    {
        try {
            return Cache::remember("User.getDepartamentName.{$this->id}", now()->addDays(), function () {
                $departament = Departament::find($this->departament_id);
                return $departament->name;
            });
        } catch (Throwable) {
            return null;
        }
    }
}
