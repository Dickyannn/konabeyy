<?php

namespace App\Models\Bpjs;

use App\Models\EmployeeKaryawan;
use App\Models\Master\ParameterBpjs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BpjsTk extends Model
{
    protected $table = 'bpjs_bpjs_tk';
    
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_karyawan',
        'periode',
        'basic_salary',
        'jht_company',
        'jp_company',
        'jkk',
        'jkm',
        'total_company',
        'jht_employee',
        'jp_employee',
        'total_employee',
        'id_parameter',
    ];

    protected $casts = [
        'periode' => 'date',
        'basic_salary' => 'decimal:2',
        'jht_company' => 'decimal:2',
        'jp_company' => 'decimal:2',
        'jkk' => 'decimal:2',
        'jkm' => 'decimal:2',
        'total_company' => 'decimal:2',
        'jht_employee' => 'decimal:2',
        'jp_employee' => 'decimal:2',
        'total_employee' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }

    public function parameter(): BelongsTo
    {
        return $this->belongsTo(ParameterBpjs::class, 'id_parameter');
    }

    /**
     * Helper: convert bulan/tahun ke format periode DATE (YYYY-MM-01)
     */
    public static function toPeriode(int $bulan, int $tahun): string
    {
        return sprintf('%04d-%02d-01', $tahun, $bulan);
    }
}
