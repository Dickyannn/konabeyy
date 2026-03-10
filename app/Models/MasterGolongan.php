<?php<?php






























}    ];        'is_active',        'deskripsi',        'gaji_pokok_max',        'gaji_pokok_min',        'nama_golongan',        'kode_golongan',    protected $fillable = [     */     * @var list<string>     *     * The attributes that are mass assignable.    /**    protected $table = 'master_golongan';     */     * Table name untuk master_golongan    /**    use HasFactory;{class MasterGolongan extends Modeluse Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Factories\HasFactory;namespace App\Models;
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterStatusKawin extends Model
{
    use HasFactory;

    /**
     * Table name untuk master_status_kawin
     */
    protected $table = 'master_status_kawin';

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
        'kode_status',
        'deskripsi',
    ];
}
