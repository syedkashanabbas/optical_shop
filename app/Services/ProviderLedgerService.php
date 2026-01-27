<?php 

namespace App\Services;

use App\Models\ProviderLedger;
use Illuminate\Support\Facades\Log;

class ProviderLedgerService
{
    public static function log($providerId, $type, $ref, $debit, $credit)
    {
        // 🟠 Step 1: Initial Log of Incoming Params
        Log::info("[ProviderLedgerService] Called with:", [
            'provider_id' => $providerId,
            'type'        => $type,
            'reference'   => $ref,
            'debit'       => $debit,
            'credit'      => $credit,
        ]);

        try {
            $last = ProviderLedger::where('provider_id', $providerId)->latest()->first();
            // $lastBalance = $last ? $last->balance : 0;
            // $balance = $lastBalance + $debit - $credit;
            
            $lastBalance = $last ? $last->balance : 0;
            $balance = $lastBalance - $debit + $credit; // <-- fixed


            // 🟢 Step 2: Log Before Create
            Log::info("[ProviderLedgerService] Creating new entry with:", [
                'provider_id' => $providerId,
                'type'        => $type,
                'reference'   => $ref,
                'date'        => now()->toDateTimeString(),
                'debit'       => $debit,
                'credit'      => $credit,
                'balance'     => $balance,
                'lastBalance' => $lastBalance
            ]);

            $entry = ProviderLedger::create([
                'provider_id' => $providerId,
                'type'        => $type,
                'reference'   => $ref,
                'date'        => now(),
                'debit'       => $debit,
                'credit'      => $credit,
                'balance'     => $balance
            ]);

            // ✅ Step 3: Confirm Created Entry
            Log::info("[ProviderLedgerService] Entry Created Successfully:", $entry->toArray());
            return $entry;

        } catch (\Exception $e) {
            // ❌ Step 4: Detailed Error Log
            Log::error("[ProviderLedgerService] ERROR: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
