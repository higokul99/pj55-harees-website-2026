<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhonePeOrderStatusCheck extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phonepe_order_status_checks';

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
        'merchant_order_id',
        'phonepe_order_id',
        'state',
        'amount',
        'response',
        'checked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'response' => 'array',
        'checked_at' => 'datetime',
    ];

    /**
     * Get the PhonePe order associated with this status check.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PhonePeOrder::class, 'merchant_order_id', 'merchant_order_id');
    }
}
