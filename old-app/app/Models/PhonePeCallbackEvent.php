<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhonePeCallbackEvent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phonepe_callback_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'event',
        'merchant_order_id',
        'merchant_refund_id',
        'state',
        'amount',
        'occurred_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'occurred_at' => 'integer',
    ];

    /**
     * Check if this is an order completion event.
     */
    public function isOrderCompleted(): bool
    {
        return $this->event === 'CHECKOUT_ORDER_COMPLETED';
    }

    /**
     * Check if this is an order failure event.
     */
    public function isOrderFailed(): bool
    {
        return $this->event === 'CHECKOUT_ORDER_FAILED';
    }

    /**
     * Check if this is a refund-related event.
     */
    public function isRefundEvent(): bool
    {
        return in_array($this->event, [
            'PG_REFUND_COMPLETED',
            'PG_REFUND_FAILED',
            'PG_REFUND_ACCEPTED'
        ]);
    }
}
