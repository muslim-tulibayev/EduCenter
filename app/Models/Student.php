<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
// // use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Student extends Authenticatable implements JWTSubject

{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'contact_no',
        'status',
        'password',
        'updated_by',
        'created_by',
    ];

    protected $hidden = [
        'password',
    ];

    public function stparents(): BelongsToMany
    {
        return $this->belongsToMany(Stparent::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

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

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
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
