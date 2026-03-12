<?php

namespace App\Models\Payroll;

use App\Models\EmployeeKaryawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insentif extends Model
{
    protected $table = 'payroll_insentif';
    
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_karyawan',
        'bulan',
        'tahun',
        'jumlah',
        'deskripsi',
        'status',
        'created_by',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }
}
