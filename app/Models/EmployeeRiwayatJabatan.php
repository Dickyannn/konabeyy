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
        'nip',
        'nama',
        'tipe_perubahan',
        'detail_perubahan',
        'current_data',
        'proposed_data',
        'tanggal_efektif',
        'jabatan_lama',
        'jabatan_baru',
        'golongan_lama',
        'golongan_baru',
        'jenis_perubahan',
        'tgl_efektif',
        'end_date',
        'nomor_sk',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tgl_efektif' => 'datetime',
        'tanggal_efektif' => 'datetime',
        'end_date' => 'datetime',
        'current_data' => 'json',
        'proposed_data' => 'json',
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
