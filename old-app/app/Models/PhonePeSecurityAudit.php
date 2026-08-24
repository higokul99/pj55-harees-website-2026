<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhonePeSecurityAudit extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phonepe_security_audit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'source',
        'reference_id',
        'authorization_received',
        'authorization_expected',
        'verified',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified' => 'boolean',
    ];

    /**
     * Create a webhook audit log.
     */
    public static function logWebhook(
        string $referenceId,
        string $authReceived,
        string $authExpected,
        bool $verified,
        ?string $remarks = null
    ): self {
        return self::create([
            'source' => 'WEBHOOK',
            'reference_id' => $referenceId,
            'authorization_received' => $authReceived,
            'authorization_expected' => $authExpected,
            'verified' => $verified,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Create an API audit log.
     */
    public static function logApi(
        string $referenceId,
        bool $verified,
        ?string $remarks = null
    ): self {
        return self::create([
            'source' => 'API',
            'reference_id' => $referenceId,
            'verified' => $verified,
            'remarks' => $remarks,
        ]);
    }
}
