<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $table = 'master_hari_libur';
    public $timestamps = false;
    
    protected $fillable = [
        'tanggal', 'keterangan'
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'tahun' => 'integer'
    ];

    public function scopeTahun($query, int $tahun)
    {
        return $query->where('tahun', $tahun);
    }
}