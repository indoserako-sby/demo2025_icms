<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'group_id',
        'name',
        'code',
        'description',
        // 'status',
        'image',
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function listData(): HasMany
    {
        return $this->hasMany(ListData::class);
    }
}
