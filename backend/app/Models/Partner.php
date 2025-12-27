<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'contact'];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}

