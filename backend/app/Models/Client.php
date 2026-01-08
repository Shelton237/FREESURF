<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'nom', 'telephone', 'type', 'email_facturation',
        'lat', 'lng', 'photos', 'partner_id', 'bts_id', 'statut',
    ];

    protected $casts = [
        'photos' => 'array',
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function bts()
    {
        return $this->belongsTo(Bts::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function eligibilites()
    {
        return $this->hasMany(Eligibilite::class);
    }

    public function installation()
    {
        return $this->hasOne(Installation::class);
    }

    public function savTickets()
    {
        return $this->hasMany(SavTicket::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function compteLiens()
    {
        return $this->hasMany(LienCompteClient::class);
    }

    public function compteClients()
    {
        return $this->belongsToMany(CompteClient::class, 'lien_compte_clients')
            ->withPivot(['statut', 'verified_at', 'last_confirmed_at'])
            ->withTimestamps();
    }
}
