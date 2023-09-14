<?php

namespace App\Models;

use App\Traits\RoleTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
// // use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Teacher extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, RoleTrait;

    public $timestamps = false;

    protected $fillable = [
        "firstname",
        "lastname",
        "email",
        "password",
        "contact",
        'role_id',
        "status",
        "is_assistant",
        'lang',
    ];

    protected $hidden = [
        'password',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    public function paymentable()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    // -------------------------------------------------- 

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
