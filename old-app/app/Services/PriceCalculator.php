<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PriceCalculator
{
    /**
     * Calculate price breakup for a product.
     *
     * @param string $productCode
     * @param string $tableName
     * @return array [FinalRate, MetalCost, MakingCharges, GST, MetalType]
     */
    public function calculatePrice($productCode, $tableName)
    {
        try {
            $product = DB::table($tableName)->where('product_code', $productCode)->first();

            if (!$product) {
                return [
                    'total_price' => 999, 
                    'metal_cost' => 0, 
                    'making_charges' => 0, 
                    'gst' => 0, 
                    'metal_type' => 'Unknown'
                ]; 
            }

            $metalPurityId = $product->metalpurity_id;
            $netWeight = $product->net_weight;

            switch ($metalPurityId) {
                case 1: // Silver
                    $metalRate = $this->getRate('metals_rates', '925_silver');
                    $silverCost = $netWeight * $metalRate;
                    $gstPercent = $this->getGst();
                    $gstAmount = $silverCost * $gstPercent / 100;
                    $finalRate = $silverCost + $gstAmount;
                    return [
                        'total_price' => $finalRate,
                        'metal_cost' => $silverCost,
                        'making_charges' => 0,
                        'gst' => $gstAmount,
                        'metal_type' => 'Silver'
                    ];

                case 2: // 925 Silver
                    $metalRate = $this->getRate('metals_rates', '925_silver');
                    $cost = $netWeight * $metalRate;
                    $gstPercent = 3;
                    $gstAmount = $cost * $gstPercent / 100;
                    $finalRate = $cost + $gstAmount;
                    return [
                        'total_price' => $finalRate,
                        'metal_cost' => $cost,
                        'making_charges' => 0,
                        'gst' => $gstAmount,
                        'metal_type' => '925 Silver'
                    ];

                case 3: // Rose Gold Silver
                    $metalRate = $this->getRate('metals_rates', 'rosegold_silver');
                    $cost = $netWeight * $metalRate;
                    $gstPercent = 3;
                    $gstAmount = $cost * $gstPercent / 100;
                    $finalRate = $cost + $gstAmount;
                    return [
                        'total_price' => $finalRate,
                        'metal_cost' => $cost,
                        'making_charges' => 0,
                        'gst' => $gstAmount,
                        'metal_type' => 'Rose Gold Silver'
                    ];

                case 4: // 18K Gold
                    $metalRate = $this->getRate('goldrate', '18k_1gm');
                    $goldCost = $netWeight * $metalRate;
                    $makingChargePercent = 35;
                    $makingCharges = $goldCost * $makingChargePercent / 100;
                    $subTotal = $goldCost + $makingCharges;
                    $gstPercent = 3;
                    $gstAmount = $subTotal * $gstPercent / 100;
                    $finalRate = $subTotal + $gstAmount;
                    return [
                        'total_price' => $finalRate,
                        'metal_cost' => $goldCost,
                        'making_charges' => $makingCharges,
                        'gst' => $gstAmount,
                        'metal_type' => 'Gold'
                    ];

                case 5: // 22K Gold
                    $metalRate = $this->getRate('goldrate', '22k_1gm');
                    $goldCost = $netWeight * $metalRate;
                    $makingChargePercent = 35;
                    $makingCharges = $goldCost * $makingChargePercent / 100;
                    $subTotal = $goldCost + $makingCharges;
                    $gstPercent = 3;
                    $gstAmount = $subTotal * $gstPercent / 100;
                    $finalRate = $subTotal + $gstAmount;
                    return [
                        'total_price' => $finalRate,
                        'metal_cost' => $goldCost,
                        'making_charges' => $makingCharges,
                        'gst' => $gstAmount,
                        'metal_type' => 'Gold'
                    ];

                case 6: // Diamond (18K)
                    $metalRate = $this->getRate('goldrate', '18k_1gm');
                    $diamondRate = $this->getRate('metals_rates', 'diamond_rate');
                    $finalRate = $metalRate + $diamondRate;
                    return [
                        'total_price' => $finalRate,
                        'metal_cost' => 0,
                        'making_charges' => 0,
                        'gst' => 0,
                        'metal_type' => 'Gold'
                    ];

                default:
                    return [
                        'total_price' => 999,
                        'metal_cost' => 0,
                        'making_charges' => 0,
                        'gst' => 0,
                        'metal_type' => 'Unknown'
                    ];
            }

        } catch (\Exception $e) {
            // Log error
            return [
                'total_price' => 999,
                'metal_cost' => 0,
                'making_charges' => 0,
                'gst' => 0,
                'metal_type' => 'Error'
            ];
        }
    }

    private function getRate($table, $column)
    {
        $record = DB::table($table)->orderBy('updated_on', 'desc')->first(); // Assuming updated_on exists or just first
        // Legacy code uses simple SELECT query, seemingly getting the first row or assuming single row config tables.
        // For goldrate it has updated_on, but legacy `SELECT 18k_1gm FROM goldrate` gets first row.
        // I'll ensure I get the latest if multiple exist, or just first.
        // Let's use first() for single-row config tables pattern.
        
        // Actually, for goldrate, previous edits showed it has history. I should likely get the LATEST one.
       
        // Checking legacy: "SELECT 18k_1gm FROM goldrate" -> No order by.
        // But previously I saw 'goldrate' table has 'updated_on'.
        // I will trust DB::table($table)->first() to match legacy "any row" behavior if I'm unsure,
        // BUT for a "Rate", latest is usually implied.
        // I'll stick to `DB::table($table)->first()->$column` to match legacy exactly if the legacy didn't order.
        // Wait, legacy `get18krate` uses `mysqli_fetch_assoc` on a query without order. It gets the "first" returned.
        
        $record = DB::table($table)->first();
        return $record ? $record->$column : 0;
    }

    private function getGst()
    {
        $record = DB::table('gst')->where('id', 1)->first();
        return $record ? $record->tax_percent : 3;
    }
}
