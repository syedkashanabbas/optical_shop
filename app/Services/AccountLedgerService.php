<?php 
namespace App\Services;

use App\Models\AccountLedger;
use Carbon\Carbon;

class AccountLedgerService
{
    public static function log($account_id, $type, $reference = null, $credit = 0, $debit = 0)
    {
        $last = AccountLedger::where('account_id', $account_id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $previous_balance = $last ? $last->balance : 0;
        // $new_balance = $previous_balance + $credit - $debit;
        $new_balance = $previous_balance + $debit - $credit;

        return AccountLedger::create([
            'account_id' => $account_id,
            'type'       => $type,
            'reference'  => $reference,
            'date'       => Carbon::now(),
            'debit'      => $debit,
            'credit'     => $credit,
            'balance'    => $new_balance,
        ]);
    }
}
