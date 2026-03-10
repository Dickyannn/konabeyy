<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeKaryawan extends Model
{
    use HasFactory;

    /**
     * Table name untuk employee_karyawan
     */
    protected $table = 'employee_karyawan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
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
        'id_unit',
        'id_atasan',
        'foto_path',
        'is_active',
    ];

    /**
     * Get the status karyawan
     */
    public function statusKaryawan(): BelongsTo
    {
        return $this->belongsTo(MasterStatusKaryawan::class, 'id_status_karyawan');
    }

    /**
     * Get the status kawin
     */
    public function statusKawin(): BelongsTo
    {
        return $this->belongsTo(MasterStatusKawin::class, 'id_status_kawin');
    }

    /**
     * Get the golongan
     */
    public function golongan(): BelongsTo
    {
        return $this->belongsTo(MasterGolongan::class, 'id_golongan');
    }

    /**
     * Get the unit
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(MasterUnitPt::class, 'id_unit');
    }

    /**
     * Get the atasan (self-join)
     */
    public function atasan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_atasan');
    }

    /**
     * Get the subordinates
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(EmployeeKaryawan::class, 'id_atasan');
    }

    /**
     * Get the positions
     */
    public function positions(): HasMany
    {
        return $this->hasMany(EmployeePosition::class, 'id_karyawan');
    }

    /**
     * Get the current position
     */
    public function currentPosition()
    {
        return $this->positions()->where('is_current', true)->first();
    }

    /**
     * Get the user account
     */
    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'id_karyawan');
    }
}
