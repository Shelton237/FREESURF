<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompteClient extends Model
{
    use HasFactory;

    protected $fillable = ['telephone', 'email', 'nom', 'statut'];

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
}

