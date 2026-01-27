<?php

namespace App\Exports;

use App\Models\ClientLedger;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;

class ClientLedgerExport implements FromCollection, WithHeadings, Responsable
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $clientId;

    public function __construct($clientId)
    {
        $this->clientId = $clientId;
    }

    public function collection()
    {
        return ClientLedger::where('client_id', $this->clientId)
            ->orderBy('date')
            ->get([
                'date',
                'reference',
                'type',
                'debit',
                'credit',
                'balance'
            ]);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'Type',
            'Debit',
            'Credit',
            'Balance',
        ];
    }
}
