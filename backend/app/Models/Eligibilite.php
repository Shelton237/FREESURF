<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eligibilite extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'user_id', 'resultat', 'commentaire'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

