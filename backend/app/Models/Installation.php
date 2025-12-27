<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'user_id', 'date', 'terminee', 'commentaire'];

    protected $casts = [
        'date' => 'date',
        'terminee' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

