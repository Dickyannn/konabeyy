<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterUnitPt extends Model
{
    use HasFactory;

    /**
     * Table name untuk master_unit_pt
     */
    protected $table = 'master_unit_pt';

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
        'kode_unit',
        'nama_pt',
        'lokasi',
        'is_active',
    ];

    /**
     * Get semua cost centers di unit ini
     */
    public function costCenters(): HasMany
    {
        return $this->hasMany(MasterCostCenter::class, 'id_unit');
    }

    /**
     * Get semua karyawan di unit ini
     */
    public function karyawans(): HasMany
    {
        return $this->hasMany(EmployeeKaryawan::class, 'id_unit');
    }

    /**
     * Get semua users di unit ini
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_unit');
    }
}
