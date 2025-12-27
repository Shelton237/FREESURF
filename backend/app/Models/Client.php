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
}
