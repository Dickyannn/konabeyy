<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * Table name untuk auth_roles
     */
    protected $table = 'auth_roles';

    /**
     * Timestamps tidak diperlukan untuk seed role
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode_role',
        'nama_role',
        'deskripsi',
    ];

    /**
     * Get semua users dengan role ini
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_role');
    }
}
