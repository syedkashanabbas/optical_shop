<?php   

namespace App\Exports;

use App\Models\AccountLedger;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;

class AccountLedgerExport implements FromCollection, WithHeadings, Responsable
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $accountId;

    public function __construct($accountId)
    {
        $this->accountId = $accountId;
    }

    public function collection()
    {
        return AccountLedger::where('account_id', $this->accountId)
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
        return ['Date', 'Reference', 'Type', 'Debit', 'Credit', 'Balance'];
    }
}
