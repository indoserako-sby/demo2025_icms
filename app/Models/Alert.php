<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'sensor_id',
        'parameter_id',
        'type',
        'value',
        'message',
        'status',
        'triggered_at',
        'acknowledged_at',
        'resolved_at'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'triggered_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];
}
