<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhonePeRefund extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phonepe_refunds';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'merchant_refund_id',
        'merchant_order_id',
        'phonepe_refund_id',
        'amount',
        'state',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
    ];

    /**
     * Get the PhonePe order associated with this refund.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PhonePeOrder::class, 'merchant_order_id', 'merchant_order_id');
    }

    /**
     * Get all status checks for this refund.
     */
    public function statusChecks(): HasMany
    {
        return $this->hasMany(PhonePeRefundStatusCheck::class, 'merchant_refund_id', 'merchant_refund_id');
    }

    /**
     * Check if the refund is completed.
     */
    public function isCompleted(): bool
    {
        return $this->state === 'COMPLETED';
    }

    /**
     * Check if the refund is pending.
     */
    public function isPending(): bool
    {
        return in_array($this->state, ['PENDING', 'CONFIRMED']);
    }

    /**
     * Check if the refund has failed.
     */
    public function isFailed(): bool
    {
        return $this->state === 'FAILED';
    }

    /**
     * Get the amount in rupees (from paisa).
     */
    public function getAmountInRupeesAttribute(): float
    {
        return $this->amount / 100;
    }
}
