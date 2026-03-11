<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ParameterBpjs extends Model
{
    protected $table = 'master_parameter_bpjs';
    protected $primaryKey = 'id';
    
    // Only created_at exists, no updated_at
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'jht_perusahaan_pct',
        'jp_perusahaan_pct',
        'jkk_pct',
        'jkm_pct',
        'jht_karyawan_pct',
        'jp_karyawan_pct',
        'bpjs_kes_perusahaan_pct',
        'bpjs_kes_karyawan_pct',
        'berlaku_mulai',
        'berlaku_selesai',
        'created_by',
    ];

    protected $casts = [
        'jht_perusahaan_pct' => 'decimal:4',
        'jp_perusahaan_pct' => 'decimal:4',
        'jkk_pct' => 'decimal:4',
        'jkm_pct' => 'decimal:4',
        'jht_karyawan_pct' => 'decimal:4',
        'jp_karyawan_pct' => 'decimal:4',
        'bpjs_kes_perusahaan_pct' => 'decimal:4',
        'bpjs_kes_karyawan_pct' => 'decimal:4',
        'berlaku_mulai' => 'datetime',
        'berlaku_selesai' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('berlaku_selesai');
    }
}
