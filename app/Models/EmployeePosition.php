<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePosition extends Model
{
    use HasFactory;

    /**
     * Table name untuk employee_position
     */
    protected $table = 'employee_position';

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
        'id_karyawan',
        'id_cost_center',
        'nama_jabatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_current',
    ];

    /**
     * Get the karyawan
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }

    /**
     * Get the cost center
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(MasterCostCenter::class, 'id_cost_center');
    }
}
