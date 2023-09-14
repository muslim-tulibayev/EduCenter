<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\RoleTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, RoleTrait;

    public $timestamps = false;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'contact',
        'role_id',
        'status',
        'password',
        'lang',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    public function paymentable()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    // ---------------------------------------------------------

    public function changes(): MorphMany
    {
        return $this->morphMany(Change::class, 'changeable');
    }

    public function makeChanges($description, $data_key, $linkedable): Change
    {
        return $this->changes()->create([
            "linkedable_id" => $linkedable->id,
            "linkedable_type" => get_class($linkedable),
            "change_description" => $description,
            "data_key" => $data_key,
            "created_at" => date('Y-m-d h:i:s'),
        ]);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
