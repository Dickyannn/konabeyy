<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFasilitasKendaraan extends Model
{
    use HasFactory;

    protected $table = 'employee_fasilitas_kendaraan';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_karyawan',
        'jenis_fasilitas',
        'nominal_allowance',
        'nomor_polisi',
        'tgl_berlaku',
        'tgl_berakhir',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'nominal_allowance' => 'decimal:2',
        'tgl_berlaku' => 'datetime',
        'tgl_berakhir' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }
}
