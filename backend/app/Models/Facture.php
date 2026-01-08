<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'compte_client_id',
        'numero',
        'periode',
        'montant',
        'statut',
        'due_date',
        'issued_at',
        'paid_at',
        'pdf_path',
        'meta',
    ];

    protected $casts = [
        'due_date' => 'date',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function compteClient()
    {
        return $this->belongsTo(CompteClient::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function scopeImpayees($query)
    {
        return $query->whereNotIn('statut', ['payee', 'annulee']);
    }

    public function isPaid(): bool
    {
        return $this->statut === 'payee';
    }
}

