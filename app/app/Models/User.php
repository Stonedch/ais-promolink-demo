<?php

namespace App\Models;

use App\Helpers\PhoneNormalizer;
use Illuminate\Support\Facades\Hash;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;
use Orchid\Support\Facades\Dashboard;

class User extends Authenticatable
{
    protected $fillable = [
        'phone',
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
        'phone' => Like::class,
        'email' => Like::class,
        'last_name' => Like::class,
        'first_name' => Like::class,
        'middle_name' => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'phone',
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
}
