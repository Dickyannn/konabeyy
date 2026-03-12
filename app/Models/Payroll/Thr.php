<?php

namespace App\Models\Payroll;

use App\Models\EmployeeKaryawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thr extends Model
{
    protected $table = 'payroll_thr';
    
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_karyawan',
        'tahun',
        'basic_salary',
        'masa_kerja_bulan',
        'jumlah_thr',
        'bulan_proses',
        'status',
        'created_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'jumlah_thr' => 'decimal:2',
        'masa_kerja_bulan' => 'integer',
        'created_at' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }
}
