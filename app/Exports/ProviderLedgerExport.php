<?php

namespace App\Exports;

use App\Models\ProviderLedger;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProviderLedgerExport implements FromCollection, WithHeadings
{
    protected $providerId;

    public function __construct($providerId)
    {
        $this->providerId = $providerId;
    }

    public function collection()
    {
        // dd(ProviderLedger::where('provider_id', $this->providerId)
        //     ->orderBy('date', 'asc')
        //     ->get([
        //         'date',
        //         'type',
        //         'reference',
        //         'debit',
        //         'credit',
        //         'balance',
        //     ]));
        return ProviderLedger::where('provider_id', $this->providerId)
            ->orderBy('date', 'asc')
            ->get([
                'date',
                'type',
                'reference',
                'debit',
                'credit',
                'balance',
            ]);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Reference',
            'Debit',
            'Credit',
            'Balance',
        ];
    }
}
