<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Card extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'card_number',
        'card_expiration',
        'card_token',
    ];

    // protected $hidden = [
    //     'card_token'
    // ];

    public function cardable(): MorphTo
    {
        return $this->morphTo();
    }
}
