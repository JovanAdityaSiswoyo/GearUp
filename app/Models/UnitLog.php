<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'units_logs';

    protected $fillable = [
        'unit_id',
        'actor_type',
        'actor_id',
        'action_type',
        'notes',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function actor()
    {
        return $this->morphTo();
    }
}
