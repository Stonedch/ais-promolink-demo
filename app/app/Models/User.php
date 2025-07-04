<?php

namespace App\Models;

use App\Helpers\PhoneNormalizer;
use App\Plugins\EntityLogger\Observers\EntityLoggerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;
use Orchid\Support\Facades\Dashboard;
use Throwable;

#[ObservedBy([EntityLoggerObserver::class])]
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
        'is_active',
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
        'is_active' => Where::class,
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
        'is_active',
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

    public function getDepartament(): ?Departament
    {
        try {
            return Cache::remember("User.getDepartament.v0.{$this->id}", now()->addHour(), function () {
                $departament = Departament::find($this->departament_id);
                return $departament;
            });
        } catch (Throwable) {
            return null;
        }
    }

    public function getDepartamentName(): ?string
    {
        try {
            return $this->getDepartament()->name;
        } catch (Throwable) {
            return null;
        }
    }

    public function avatar(): ?Attachment
    {
        try {
            return Attachment::find($this->attachment_id);
        } catch (Throwable) {
            return null;
        }
    }

    public function getFullname(): ?string
    {
        try {
            return implode(' ', [
                $this->last_name,
                $this->first_name,
                $this->middle_name
            ]);
        } catch (Throwable) {
            return null;
        }
    }

    public function departament()
    {
        return $this->belongsTo(Departament::class);
    }
}
