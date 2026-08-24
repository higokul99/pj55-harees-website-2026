<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhonePePaymentAttempt extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phonepe_payment_attempts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'merchant_order_id',
        'transaction_id',
        'payment_mode',
        'state',
        'amount',
        'timestamp',
        'error_code',
        'detailed_error_code',
        'raw_response',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'timestamp' => 'integer',
        'raw_response' => 'array',
    ];

    /**
     * Get the PhonePe order associated with this attempt.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PhonePeOrder::class, 'merchant_order_id', 'merchant_order_id');
    }

    /**
     * Check if the attempt is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->state === 'COMPLETED';
    }

    /**
     * Check if the attempt has failed.
     */
    public function isFailed(): bool
    {
        return $this->state === 'FAILED';
    }
}
