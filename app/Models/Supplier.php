<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'raison_social',
        'adresse',
        'tele',
        'email',
        'description',
    ];
}
