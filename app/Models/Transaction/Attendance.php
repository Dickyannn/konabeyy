<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeKaryawan;
use App\Models\Master\ShiftKerja;

class Attendance extends Model
{
    protected $table = 'transaction_attendance';
    public $timestamps = false;
    
    protected $fillable = [
        'id_karyawan', 'tanggal', 'id_shift', 'status',
        'check_in', 'check_out', 'terlambat_menit',
        'keterangan', 'source', 'created_by',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }

    public function shift()
    {
        return $this->belongsTo(ShiftKerja::class, 'id_shift');
    }

    // Scopes
    public function scopeHariIni($query)
    {
        return $query->where('tanggal', today());
    }

    public function scopeBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }

    public function scopeHadir($query)
    {
        return $query->where('status', 'Hadir');
    }
}