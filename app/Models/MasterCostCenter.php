<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterCostCenter extends Model
{
    use HasFactory;

    /**
     * Table name untuk master_cost_center
     */
    protected $table = 'master_cost_center';

    /**
     * Timestamps: hanya created_at
     */
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_unit',
        'kode_cc',
        'nama_cc',
        'is_active',
    ];

    /**
     * Get unit PT yang memiliki division ini
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(MasterUnitPt::class, 'id_unit');
    }

    /**
     * Get semua jabatan di cost center ini
     */
    public function positions(): HasMany
    {
        return $this->hasMany(EmployeePosition::class, 'id_cost_center');
    }
}
