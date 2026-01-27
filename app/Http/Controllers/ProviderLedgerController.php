<?php

namespace App\Http\Controllers;

use App\Models\ProviderLedger;
use App\Exports\ProviderLedgerExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProviderLedgerController extends Controller
{
    /**
     * Export ledger for a specific provider as Excel
     */
    public function export($providerId)
    {
        return Excel::download(new ProviderLedgerExport($providerId), 'provider_ledger_' . $providerId . '.xlsx');
    }

    /**
     * (Optional) Show ledger view for a provider
     */
    public function index($providerId)
    {
        $ledgers = ProviderLedger::where('provider_id', $providerId)
            ->orderBy('date', 'asc')
            ->get();

        return view('providers.ledger', compact('ledgers', 'providerId'));
    }
}
