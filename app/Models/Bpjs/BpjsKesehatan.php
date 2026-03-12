<?php

namespace App\Models\Bpjs;

use App\Models\EmployeeKaryawan;
use App\Models\Master\ParameterBpjs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BpjsKesehatan extends Model
{
    protected $table = 'bpjs_bpjs_kesehatan';
    
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_karyawan',
        'periode',
        'basic_salary',
        'beban_perusahaan',
        'beban_karyawan',
        'id_parameter',
    ];

    protected $casts = [
        'periode' => 'date',
        'basic_salary' => 'decimal:2',
        'beban_perusahaan' => 'decimal:2',
        'beban_karyawan' => 'decimal:2',
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
