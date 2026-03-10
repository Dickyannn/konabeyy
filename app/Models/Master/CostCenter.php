<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostCenter extends Model
{
    protected $table = 'master_cost_center';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id_unit_pt',
        'kode_cc',
        'nama_cc',
        'lokasi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitPt::class, 'id_unit_pt');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
