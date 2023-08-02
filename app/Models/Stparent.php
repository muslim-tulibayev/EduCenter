<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
// // use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Stparent extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'contact_no',
    ];

    protected $hidden = [
        'password',
    ];

    public function fullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    // public function changes(): MorphMany
    // {
    //     return $this->morphMany(Change::class, 'changeable');
    // }

    // public function makeChanges($description, $data_key, $linkedable): Change
    // {
    //     return $this->changes()->create([
    //         "linkedable_id" => $linkedable->id,
    //         "linkedable_type" => get_class($linkedable),
    //         "change_description" => $description,
    //         "data_key" => $data_key,
    //         "created_at" => date('Y-m-d h:i:s'),
    //     ]);
    // }

    public function changes()
    {
        return $this->morphMany(Change::class, 'changeable');
    }

    public function makeChange($description, $dataKey, $linkedable)
    {
        $change = $this->changes()->create([
            'change_description' => $description,
            'data_key' => $dataKey,
            'linkedable_id' => $linkedable->id,
            'linkedable_type' => get_class($linkedable),
        ]);

        return $change;
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