<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'cashier_id',
        'cashier_key',
    ];
}
