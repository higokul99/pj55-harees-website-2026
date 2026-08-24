<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhonePeRefundStatusCheck extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phonepe_refund_status_checks';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'merchant_refund_id',
        'state',
        'response',
        'checked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'response' => 'array',
        'checked_at' => 'datetime',
    ];

    /**
     * Get the refund associated with this status check.
     */
    public function refund(): BelongsTo
    {
        return $this->belongsTo(PhonePeRefund::class, 'merchant_refund_id', 'merchant_refund_id');
    }
}
