<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'compte_client_id', 'client_id', 'type', 'statut', 'nom', 'telephone',
        'email_facturation', 'adresse', 'lat', 'lng', 'commentaire',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function compteClient()
    {
        return $this->belongsTo(CompteClient::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

