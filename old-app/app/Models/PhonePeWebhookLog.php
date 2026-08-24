<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhonePeWebhookLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phonepe_webhook_logs';

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
        'event_type',
        'merchant_order_id',
        'phonepe_order_id',
        'merchant_refund_id',
        'authorization_header',
        'verified',
        'payload',
        'received_at',
        'processing_status',
        'failure_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified' => 'boolean',
        'payload' => 'array',
        'received_at' => 'datetime',
    ];

    /**
     * Check if the webhook was successfully verified.
     */
    public function isVerified(): bool
    {
        return $this->verified === true;
    }

    /**
     * Check if the webhook has been processed.
     */
    public function isProcessed(): bool
    {
        return $this->processing_status === 'PROCESSED';
    }

    /**
     * Mark webhook as processed.
     */
    public function markAsProcessed(): void
    {
        $this->update(['processing_status' => 'PROCESSED']);
    }

    /**
     * Mark webhook as failed with reason.
     */
    public function markAsFailed(string $reason): void
    {
        $this->update([
            'processing_status' => 'FAILED',
            'failure_reason' => $reason,
        ]);
    }
}
