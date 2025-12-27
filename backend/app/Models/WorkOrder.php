<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'type','client_id','bts_id','assigned_to','status','scheduled_at','started_at','completed_at','lat','lng','notes','checklist'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'checklist' => 'array',
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function client(){ return $this->belongsTo(Client::class); }
    public function bts(){ return $this->belongsTo(Bts::class); }
    public function technician(){ return $this->belongsTo(User::class,'assigned_to'); }
    public function events(){ return $this->hasMany(WorkOrderEvent::class); }
    public function attachments(){ return $this->hasMany(WorkOrderAttachment::class); }
}

