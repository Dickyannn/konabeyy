<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAnggotaKeluarga extends Model
{
    protected $table = 'employee_anggota_keluarga';
    
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_karyawan',
        'nama',
        'nik',
        'status_keluarga',
        'tanggal_lahir',
        'is_tanggungan',
        'is_active',
        'tanggal_nonaktif',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_nonaktif' => 'date',
        'is_tanggungan' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Status keluarga options
    public static function getStatusKeluargaOptions()
    {
        return [
            'spouse' => 'Suami/Istri',
            'child' => 'Anak',
            'father' => 'Ayah',
            'mother' => 'Ibu',
            'in-law' => 'Mertua',
        ];
    }
}