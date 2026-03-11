<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterGolongan extends Model
{
    use HasFactory;

    /**
     * Table name untuk master_golongan
     */
    protected $table = 'master_golongan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode_golongan',
        'nama_golongan',
        'gaji_pokok_min',
        'gaji_pokok_max',
        'deskripsi',
        'is_active',
    ];
}

