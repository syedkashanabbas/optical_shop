<?php

namespace App\Services;

use App\Models\ProductLedger;
use Illuminate\Support\Facades\Log;

class LedgerService
{
    /**
     * Log stock movement in the product ledger.
     *
     * @param int $productId
     * @param string $type        // e.g., 'adjustment', 'sale', 'purchase'
     * @param string|null $ref    // e.g., 'ADJ#55', 'INV#001'
     * @param int $in             // quantity added
     * @param int $out            // quantity removed
     * @param int $customerName      
     * @param int $product_code      
     * @return ProductLedger|null
     */
  

public static function log($productId, $type, $ref, $in, $out, $customerName = null, $productCode = null)
{
    try {
        Log::info("LedgerService called with:", [
            'product_id'    => $productId,
            'type'          => $type,
            'ref'           => $ref,
            'in'            => $in,
            'out'           => $out,
            'customer_name' => $customerName,
            'product_code'  => $productCode,
        ]);

        $last = ProductLedger::where('product_id', $productId)->latest()->first();
        $lastBalance = $last && $last->balance !== null ? $last->balance : 0;

        $balance = $lastBalance + $in - $out;

        $entry = ProductLedger::create([
            'product_id'    => $productId,
            'type'          => $type,
            'reference'     => $ref,
            'date'          => now(),
            'quantity_in'   => $in,
            'quantity_out'  => $out,
            'balance'       => $balance,
            'customer_name' => $customerName,
            'product_code'  => $productCode,
        ]);

        Log::info("Ledger entry created:", $entry->toArray());

        return $entry;

    } catch (\Throwable $e) {
        Log::error("LedgerService Error: " . $e->getMessage(), [
            'product_id'    => $productId,
            'type'          => $type,
            'ref'           => $ref,
            'in'            => $in,
            'out'           => $out,
            'customer_name' => $customerName,
            'product_code'  => $productCode,
        ]);
        return null;
    }
}

    }
