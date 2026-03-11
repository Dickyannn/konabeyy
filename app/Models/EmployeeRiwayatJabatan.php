<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeRiwayatJabatan extends Model
{
    use HasFactory;

    protected $table = 'employee_riwayat_jabatan';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_karyawan',
        'jabatan_lama',
        'jabatan_baru',
        'golongan_lama',
        'golongan_baru',
        'jenis_perubahan',
        'tgl_efektif',
        'nomor_sk',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tgl_efektif' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }

    public function golonganLamaRelation(): BelongsTo
    {
        return $this->belongsTo(MasterGolongan::class, 'golongan_lama');
    }

    public function golonganBaruRelation(): BelongsTo
    {
        return $this->belongsTo(MasterGolongan::class, 'golongan_baru');
    }
}
