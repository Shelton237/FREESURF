<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderAttachment extends Model
{
    use HasFactory;
    protected $fillable = ['work_order_id','path','mime','meta'];
    protected $casts = ['meta' => 'array'];
    public function workOrder(){ return $this->belongsTo(WorkOrder::class); }
}

