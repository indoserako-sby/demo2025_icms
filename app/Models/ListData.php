<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListData extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'group_id',
        'asset_id',
        'machine_parameter_id',
        'position_id',
        'datvar_id',
        'value',
        'state',
        'condition',
        'warning_limit',
        'danger_limit',
    ];

    /**
     * Get the area that owns the list data.
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Get the group that owns the list data.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the asset that owns the list data.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the machine parameter that owns the list data.
     */
    public function machineParameter()
    {
        return $this->belongsTo(MachineParameter::class);
    }

    /**
     * Get the position that owns the list data.
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the datvar that owns the list data.
     */
    public function datvar()
    {
        return $this->belongsTo(Datvar::class);
    }

    /**
     * Get the data alarms associated with this list data.
     */
    public function dataAlarms()
    {
        return $this->hasMany(DataAlarm::class, 'list_data_id');
    }
}
