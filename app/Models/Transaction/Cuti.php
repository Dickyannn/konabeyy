<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeKaryawan;

class Cuti extends Model
{
    protected $table = 'transaction_cuti';
    public $timestamps = false;
    
    protected $fillable = [
        'id_karyawan', 'jenis_cuti', 'tanggal_mulai', 'tanggal_selesai',
        'alasan', 'status', 'approved_by', 'approved_at',
        'catatan_approver', 'dokumen_path',
    ];
    
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'jumlah_hari' => 'integer',
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
}