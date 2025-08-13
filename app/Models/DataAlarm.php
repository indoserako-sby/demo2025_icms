<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAlarm extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'list_data_id',
        'alert_type',
        'start_time',
        'end_time',
        'resolved',
        'acknowledged',
        'acknowledged_by',
        'acknowledged_at',
        'notes',
        'alarm_cause',
        'value',
        'warning',
        'danger',
        'machine_person',
        'starttimemaintenance',
        'endtimemaintenance'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'resolved' => 'boolean',
        'acknowledged' => 'boolean',
        'acknowledged_at' => 'datetime',
        'starttimemaintenance' => 'datetime',
        'endtimemaintenance' => 'datetime',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the list data associated with this alarm.
     */
    public function listData()
    {
        return $this->belongsTo(ListData::class, 'list_data_id');
    }

    /**
     * Get the user who acknowledged this alarm.
     */
    public function acknowledgedByUser()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Check if the alarm is active (not resolved)
     */
    public function isActive()
    {
        return !$this->resolved;
    }

    /**
     * Scope a query to only include active alarms.
     */
    public function scopeActive($query)
    {
        return $query->where('resolved', false);
    }

    /**
     * Scope a query to only include warning alarms.
     */
    public function scopeWarnings($query)
    {
        return $query->where('alert_type', 'warning');
    }

    /**
     * Scope a query to only include danger alarms.
     */
    public function scopeDangers($query)
    {
        return $query->where('alert_type', 'danger');
    }
}
