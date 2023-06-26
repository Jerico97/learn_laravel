<?php

namespace Shops\Domain\Models;

use Shops\Contracts\DataTransferObjects\ShopDto;
use App\Casts\Hash;
use App\Casts\Json;
use App\Casts\PhoneNumber;
use App\Helpers\DomainModel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;

class Shop extends DomainModel 
{
    use HasFactory;

    public function fillableRules(): array
    {
        return [
            'title' => ['required'],
            'url' => ['required']
        ];
    }

    public function toDto(): mixed
    {
        return new ShopDto(
                id: $this->id,
                title: $this->title,
                url: $this->url,
                created_at: $this->created_at,
                updated_at: $this->updated_at
        );
    }

    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeMaybeFilterRole($query, ?string $role)
    {
        if ($role) {
            $query->where('role', '=', $role);
        }
    }

    public function scopeMaybeSearch($query, ?string $q)
    {
        if ($q !== null and $q !== '') {
            if (str_contains($q, '@')) {
                $query->where('email', 'like', "%{$q}%");
            } elseif (preg_match('~^[0-9\.\-\+\ ]+$~', $q)) {
                $phone_value = preg_replace('~[^0-9\.\-\+\ ]+~', '', $q);
                $query->where('phone', 'like', "{$phone_value}%");
            } else {
                $query->where('name', 'like', "%{$q}%");
            }
        }
    }
}
