<?php 
namespace App\Services;

use App\Models\ClientLedger;
use Illuminate\Support\Facades\Log;

class ClientLedgerService
{
    public static function log($clientId, $type, $ref, $debit = 0, $credit = 0, $note = null)
    {
        try {
            Log::info("ClientLedgerService called:", [
                'client_id' => $clientId,
                'type'      => $type,
                'ref'       => $ref,
                'debit'     => $debit,
                'credit'    => $credit,
                'note'      => $note
            ]);

            $last = ClientLedger::where('client_id', $clientId)->latest()->first();
            $lastBalance = $last ? $last->balance : 0;

            $balance = $lastBalance + $debit - $credit;

            $entry = ClientLedger::create([
                'client_id' => $clientId,
                'type'      => $type,
                'reference' => $ref,
                'date'      => now(),
                'debit'     => $debit,
                'credit'    => $credit,
                'balance'   => $balance,
                'note'      => $note,
            ]);

            Log::info("Client ledger entry created", $entry->toArray());
            return $entry;
        } catch (\Throwable $e) {
            Log::error("ClientLedgerService Error: " . $e->getMessage());
        }
    }
}
