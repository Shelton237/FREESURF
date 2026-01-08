<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LienCompteClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'compte_client_id',
        'client_id',
        'statut',
        'verified_at',
        'last_confirmed_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'last_confirmed_at' => 'datetime',
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

