<?php
namespace App\Http\Controllers;

use App\Exports\AccountLedgerExport;

class AccountLedgerController extends Controller
{
    public function export($id)
    {
        $fileName = 'AccountLedger_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return (new AccountLedgerExport($id))->download($fileName);
    }
}
