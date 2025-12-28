<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'work_order_id',
        'assigned_to',
        'type',
        'channel',
        'priority',
        'status',
        'subject',
        'description',
        'resolution_notes',
        'follow_up_at',
        'resolved_at',
    ];

    protected $casts = [
        'follow_up_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
