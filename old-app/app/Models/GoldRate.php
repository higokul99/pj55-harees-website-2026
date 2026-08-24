<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goldrate extends Model
{
    use HasFactory;

    protected $table = 'goldrate'; // Use goldrate table instead of gold_rates
    public $timestamps = false; // goldrate table uses updated_on instead of created_at/updated_at

    protected $fillable = ['18k_1gm', '22k_1gm', 'updated_on'];

    protected $casts = [
        '18k_1gm' => 'integer',
        '22k_1gm' => 'integer',
        'updated_on' => 'datetime',
    ];

    /**
     * Get the latest rate for a specific purity.
     * Since goldrate table stores rates as columns, not rows,
     * we get the latest record and return the appropriate column value.
     * 
     * @param int $purityId The metalpurity_id from metals_purity table
     * @return object|null Returns object with rate_per_gram property for compatibility
     */
    public static function getLatestRate($purityId)
    {
        $latestRate = self::orderBy('updated_on', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if (!$latestRate) {
            return null;
        }

        // Get the purity name to determine which column to use
        $purity = \App\Models\MetalsPurity::find($purityId);

        if (!$purity) {
            return null;
        }

        // Map purity name to column
        $column = null;
        if (stripos($purity->name, '18K') !== false || stripos($purity->name, '18 K') !== false) {
            $column = '18k_1gm';
        } elseif (stripos($purity->name, '22K') !== false || stripos($purity->name, '22 K') !== false) {
            $column = '22k_1gm';
        }

        if (!$column) {
            return null;
        }

        // Return an object with rate_per_gram property for compatibility with existing code
        return (object) [
            'rate_per_gram' => $latestRate->$column,
            'effective_date' => $latestRate->updated_on,
        ];
    }
}
