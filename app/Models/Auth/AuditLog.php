<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'auth_audit_log';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'action',
        'table_name',
        'record_id',
        'id_user',
        'ip_address',
        'user_agent',
        'details',
        'old_data',
        'new_data',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user');
    }

    // Scopes
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Static method to log audit trail
     */
    public static function catat(string $action, string $tableName, $recordId, array $details = [])
    {
        return self::create([
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'id_user' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => json_encode($details),
            'created_at' => now(),
        ]);
    }
}
