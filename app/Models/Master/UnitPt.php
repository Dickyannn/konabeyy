<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitPt extends Model
{
    protected $table = 'master_unit_pt';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'kode_unit',
        'nama_pt',
        'lokasi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function costCenters(): HasMany
    {
        return $this->hasMany(CostCenter::class, 'id_unit');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
