<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ShiftKerja extends Model
{
    protected $table = 'master_shift_kerja';
    public $timestamps = false;
    
    protected $fillable = [
        'nama_shift', 'jam_masuk', 'jam_pulang', 'toleransi_menit', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}