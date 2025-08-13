<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineParameter extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Get the datactual records for this parameter.
     */
    public function datactuals()
    {
        return $this->hasMany(Datactual::class, 'parameter_id');
    }
}
