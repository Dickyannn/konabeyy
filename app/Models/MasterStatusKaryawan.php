<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterStatusKaryawan extends Model
{
    use HasFactory;

    /**
     * Table name untuk master_status_karyawan
     */
    protected $table = 'master_status_karyawan';

    /**
     * Timestamps: hanya created_at
     */
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_status',
    ];
}
