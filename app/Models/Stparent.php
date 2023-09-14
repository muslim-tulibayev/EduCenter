<?php

namespace App\Models;

// // use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\RoleTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Stparent extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, RoleTrait;

    public $timestamps = false;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'contact',
        'role_id',
        'lang',
    ];

    protected $hidden = [
        'password',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    public function cards(): MorphMany
    {
        return $this->morphMany(Card::class, 'cardable');
    }

    public function paymentable()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    // public function getBranchesAttribute()
    // {
    //     return $this->students->flatMap(function ($student) {
    //         return $student->branches;
    //     })->unique();
    // }

    // public function branches()
    // {
    //     return $this->students->flatMap(function ($student) {
    //         return $student->branches;
    //     })->unique();
    // }

    // ----------------------------------------------------------

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
