<?php

namespace App\Models\Payroll;

use App\Models\EmployeeKaryawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRecord extends Model
{
    protected $table = 'payroll_payroll';
    
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_karyawan',
        'bulan',
        'tahun',
        'basic_salary',
        'total_income',
        'total_deduction',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PayrollDetail::class, 'id_payroll');
    }
}
