<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogData extends Model
{
    use HasFactory;

    protected $table = 'log_data';

    protected $fillable = [
        'area_id',
        'group_id',
        'asset_id',
        'list_data_id',
        'value',
        'date',
        'state',
        'condition',
        'unit'
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'float'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function listData()
    {
        return $this->belongsTo(ListData::class, 'list_data_id');
    }
}
