<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObsMasterDataKaryawan extends Model
{
    protected $table = 'obs_master_data_karyawan';
    
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_karyawan',
        'action_type',
        'change_reason',
        'nip',
        'nik',
        'npwp',
        'nama_karyawan',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'email',
        'nomor_telepon',
        'bpjs_kesehatan_number',
        'bpjs_tk_number',
        'tanggal_masuk',
        'tanggal_keluar',
        'id_status_karyawan',
        'id_status_kawin',
        'id_golongan',
        'jabatan',
        'id_unit',
        'id_atasan',
        'foto_path',
        'is_active',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function statusKaryawan(): BelongsTo
    {
        return $this->belongsTo(MasterStatusKaryawan::class, 'id_status_karyawan');
    }

    public function statusKawin(): BelongsTo
    {
        return $this->belongsTo(MasterStatusKawin::class, 'id_status_kawin');
    }

    public function golongan(): BelongsTo
    {
        return $this->belongsTo(MasterGolongan::class, 'id_golongan');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(MasterUnitPt::class, 'id_unit');
    }

    public function atasan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_atasan');
    }
}