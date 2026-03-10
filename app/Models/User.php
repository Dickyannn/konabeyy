<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Table name untuk auth_users
     */
    protected $table = 'auth_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'id_role',
        'id_unit',
        'id_karyawan',
        'is_active',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the role yang dimiliki user
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    /**
     * Get unit perusahaan (nullable untuk superadmin)
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(MasterUnitPt::class, 'id_unit');
    }

    /**
     * Get employee/karyawan (nullable untuk user sistem)
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'id_karyawan');
    }

    /**
     * Scope: Filter user yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter user berdasarkan role
     */
    public function scopeByRole($query, $roleCode)
    {
        return $query->whereHas('role', fn($q) => $q->where('kode_role', $roleCode));
    }

    /**
     * Check: Apakah user adalah master system admin
     */
    public function isMasterSystem(): bool
    {
        return $this->role?->kode_role === 'master_system';
    }

    /**
     * Check: Apakah user adalah personal admin
     */
    public function isPersonalAdmin(): bool
    {
        return $this->role?->kode_role === 'personal_admin';
    }

    /**
     * Check: Apakah user adalah payroll admin
     */
    public function isPayrollAdmin(): bool
    {
        return $this->role?->kode_role === 'payroll';
    }

    /**
     * Check: Apakah user adalah personalia
     */
    public function isPersonalia(): bool
    {
        return $this->role?->kode_role === 'personalia';
    }
}
