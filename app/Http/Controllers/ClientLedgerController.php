<?php

namespace App\Http\Controllers;

use App\Exports\ClientLedgerExport;
use Illuminate\Http\Request;

class ClientLedgerController extends Controller
{

public function export($id)
{
    $fileName = 'ClientLedger_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    return (new ClientLedgerExport($id))->download($fileName);
}

}

