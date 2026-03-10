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
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
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
}
