<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeKaryawan;

class Lembur extends Model
{
    protected $table = 'transaction_lembur';
    public $timestamps = false;
    
    protected $fillable = [
        'id_karyawan', 'tanggal', 'jam_mulai', 'jam_selesai', 'total_jam',
        'keterangan', 'status', 'approved_by', 'approved_at',
        'nominal_lembur', 'sudah_dibayar', 'created_by',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'total_jam' => 'float',
        'nominal_lembur' => 'float',
        'sudah_dibayar' => 'boolean',
    ];

    public function karyawan()
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }

    public function approver()
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }
}