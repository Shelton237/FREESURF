<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompteClient extends Model
{
    use HasFactory;

    protected $fillable = ['telephone', 'email', 'nom', 'statut', 'password'];

    protected $hidden = ['password'];

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }

    public function liens()
    {
        return $this->hasMany(LienCompteClient::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'lien_compte_clients')
            ->withPivot(['statut', 'verified_at', 'last_confirmed_at'])
            ->withTimestamps();
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }
}
