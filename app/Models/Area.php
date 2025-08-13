<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = [
        'name',
        'description',
        'location',
        'image'
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function liveData(): HasMany
    {
        return $this->hasMany(LiveData::class);
    }
}
