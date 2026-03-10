<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Master\CarAllowance;
use App\Models\Master\KomponenTunjangan;

class Golongan extends Model
{
    protected $table = 'master_golongan';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'kode_golongan',
        'nama_golongan',
        'gaji_pokok_min',
        'gaji_pokok_max',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'gaji_pokok_min' => 'decimal:2',
        'gaji_pokok_max' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function carAllowances(): HasMany
    {
        return $this->hasMany(CarAllowance::class, 'id_golongan');
    }

    public function komponenGaji(): HasMany
    {
        return $this->hasMany(KomponenTunjangan::class, 'id_golongan');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
