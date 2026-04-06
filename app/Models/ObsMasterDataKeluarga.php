<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObsMasterDataKeluarga extends Model
{
    protected $table = 'obs_master_data_keluarga';
    
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'employee_id',
        'id_anggota_keluarga',
        'action_type',
        'action_update',
        'change_reason',
        'nik',
        'nama',
        'status_keluarga',
        'tanggal_lahir',
        'is_tanggungan',
        'is_active',
        'tanggal_nonaktif',
        'created_by',
        'notes',
        'data_before',
        'data_after',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_nonaktif' => 'date',
        'is_tanggungan' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'data_before' => 'array',
        'data_after' => 'array',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeKaryawan::class, 'employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public static function logFamilyChange($employeeId, $actionType, $dataBefore = null, $dataAfter = null)
    {
        try {
            // Get authenticated user ID with validation
            $userId = auth()->id();
            
            // Verify user exists
            if ($userId && !\App\Models\User::where('id', $userId)->exists()) {
                $userId = null;
            }
            
            // Fallback to first available user
            if (!$userId) {
                $firstUser = \App\Models\User::first();
                $userId = $firstUser ? $firstUser->id : null;
            }
            
            // If still no valid user, default to 1 but only if it exists
            if (!$userId) {
                if (\App\Models\User::where('id', 1)->exists()) {
                    $userId = 1;
                } else {
                    // Skip audit if no valid user found
                    return null;
                }
            }
            
            return self::create([
                'employee_id' => $employeeId,
                'action_type' => $actionType,
                'action_update' => 'proposed',
                'change_reason' => 'Family data change',
                'data_before' => $dataBefore,
                'data_after' => $dataAfter,
                'created_by' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error logging family change: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log family member change with individual fields
     */
    public static function logFamilyMemberChange($employeeId, $actionType, $anggotaKeluargaId = null, $changeReason = null, $dataBefore = null, $dataAfter = null)
    {
        try {
            // Get authenticated user ID
            $userId = auth()->id();
            
            // If no authenticated user, try to find any valid user
            if (!$userId) {
                $firstUser = \App\Models\User::first();
                $userId = $firstUser ? $firstUser->id : null;
            }
            
            // If still no user, use default ID 1
            if (!$userId) {
                $userId = 1;
            }
            
            // Prepare snapshot data
            $snapshot = $dataBefore ?: ($dataAfter ?: []);
            
            return self::create([
                'employee_id' => $employeeId,
                'id_anggota_keluarga' => $anggotaKeluargaId,
                'action_type' => $actionType,
                'action_update' => 'proposed',
                'change_reason' => $changeReason ?? ucwords(str_replace('_', ' ', $actionType)) . ' data keluarga',
                
                // Snapshot individual fields
                'nik' => $snapshot['nik'] ?? null,
                'nama' => $snapshot['nama'] ?? null,
                'status_keluarga' => $snapshot['status_keluarga'] ?? null,
                'tanggal_lahir' => $snapshot['tanggal_lahir'] ?? null,
                'is_tanggungan' => $snapshot['is_tanggungan'] ?? false,
                'is_active' => $snapshot['is_active'] ?? true,
                'tanggal_nonaktif' => $snapshot['tanggal_nonaktif'] ?? null,
                
                // Audit fields
                'created_by' => $userId,
                'data_before' => $dataBefore,
                'data_after' => $dataAfter,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error logging family member change: ' . $e->getMessage());
            return null;
        }
    }
}
