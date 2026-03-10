<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarAllowance extends Model
{
    protected $table = 'master_car_allowance';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id_golongan',
        'nominal',
        'berlaku_mulai',
        'berlaku_selesai',
        'created_by',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'berlaku_mulai' => 'date',
        'berlaku_selesai' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function golongan(): BelongsTo
    {
        return $this->belongsTo(Golongan::class, 'id_golongan');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('berlaku_selesai');
    }
}
