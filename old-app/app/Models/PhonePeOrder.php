<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhonePeOrder extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phonepe_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'merchant_order_id',
        'phonepe_order_id',
        'amount',
        'currency',
        'state',
        'last_known_state',
        'expire_at',
        'redirect_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'expire_at' => 'integer',
    ];

    /**
     * Get all payment attempts for this order.
     */
    public function paymentAttempts(): HasMany
    {
        return $this->hasMany(PhonePePaymentAttempt::class, 'merchant_order_id', 'merchant_order_id');
    }

    /**
     * Get all status checks for this order.
     */
    public function statusChecks(): HasMany
    {
        return $this->hasMany(PhonePeOrderStatusCheck::class, 'merchant_order_id', 'merchant_order_id');
    }

    /**
     * Get all refunds for this order.
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(PhonePeRefund::class, 'merchant_order_id', 'merchant_order_id');
    }

    /**
     * Check if the order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->state === 'COMPLETED';
    }

    /**
     * Check if the order is pending.
     */
    public function isPending(): bool
    {
        return $this->state === 'PENDING';
    }

    /**
     * Check if the order has failed.
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
