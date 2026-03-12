<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDetail extends Model
{
    protected $table = 'payroll_payroll_detail';
    
    public $timestamps = false;

    protected $fillable = [
        'id_payroll',
        'id_component',
        'amount',
        'keterangan',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(PayrollRecord::class, 'id_payroll');
    }
}
