<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeKaryawan;

class SaldoCuti extends Model
{
    protected $table = 'transaction_saldo_cuti';
    public $timestamps = false;
    
    protected $fillable = [
        'id_karyawan', 'tahun', 'kuota', 'terpakai'
    ];
    
    protected $casts = [
        'sisa' => 'integer',
        'updated_at' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }
}