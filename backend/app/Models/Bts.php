<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bts extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'ville', 'lat', 'lng', 'composants', 'photos',
    ];

    protected $casts = [
        'composants' => 'array',
        'photos' => 'array',
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}

