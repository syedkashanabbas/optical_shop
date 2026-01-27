<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\PaymentPurchase;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LedgerExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $runningBalance = 0;
    protected $balances = []; // running balance per supplier

    protected $start_date;
    protected $end_date;
    protected $Ref;
    protected $payment_status;
    protected $provider_id;
    protected $warehouse_id;

    public function __construct($filters = [])
    {
        $this->start_date = $filters['start_date'] ?? null;
        $this->end_date = $filters['end_date'] ?? null;
        $this->Ref = $filters['Ref'] ?? null;
        $this->payment_status = $filters['payment_status'] ?? null;
        $this->provider_id = $filters['provider_id'] ?? null;
        $this->warehouse_id = $filters['warehouse_id'] ?? null;
    }

    public function collection()
    {
        // PURCHASES → increase supplier balance (CREDIT)
        $purchaseRows = Purchase::with('provider')
            ->when($this->start_date, fn($q) => $q->whereDate('date', '>=', $this->start_date))
            ->when($this->end_date, fn($q) => $q->whereDate('date', '<=', $this->end_date))
            ->when($this->Ref, fn($q) => $q->where('Ref', 'like', "%{$this->Ref}%"))
            ->when($this->provider_id, fn($q) => $q->where('provider_id', $this->provider_id))
            ->when($this->warehouse_id, fn($q) => $q->where('warehouse_id', $this->warehouse_id))
            ->get()
            ->map(function ($purchase) {
                return (object) [
                    'date' => $purchase->date,
                    'Ref' => $purchase->Ref,
                    'supplier_id' => $purchase->provider_id,
                    'supplier_name' => optional($purchase->provider)->name,
                    'credit' => (float) $purchase->GrandTotal, // PURCHASE = CREDIT
                    'debit' => 0,                             // no payment here
                ];
            });

        // PAYMENTS → decrease supplier balance (DEBIT)
        $paymentRows = PaymentPurchase::with('purchase.provider')
            ->when($this->start_date, fn($q) => $q->whereDate('date', '>=', $this->start_date))
            ->when($this->end_date, fn($q) => $q->whereDate('date', '<=', $this->end_date))
            ->when($this->Ref, fn($q) => $q->where('Ref', 'like', "%{$this->Ref}%"))
            ->when($this->provider_id, fn($q) => $q->whereHas('purchase', fn($q2) => $q2->where('provider_id', $this->provider_id)))
            ->when($this->warehouse_id, fn($q) => $q->whereHas('purchase', fn($q2) => $q2->where('warehouse_id', $this->warehouse_id)))
            ->get()
            ->map(function ($payment) {
                return (object) [
                    'date' => $payment->date,
                    'Ref' => $payment->Ref,
                    'supplier_id' => optional($payment->purchase)->provider_id,
                    'supplier_name' => optional($payment->purchase->provider)->name,
                    'credit' => 0,                             // no purchase here
                    'debit' => (float) $payment->montant,     // PAYMENT = DEBIT
                ];
            });

        // COMBINE + SORT
        return $purchaseRows->merge($paymentRows)->sortBy('date')->values();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Ref',
            'Supplier',
            'Credit',
            'Debit',
            'Balance',
        ];
    }

    public function map($row): array
    {
        $supplierId = $row->supplier_id;

        if (!isset($this->balances[$supplierId])) {
            $this->balances[$supplierId] = 0;
        }

        // Balance = old + credit – debit
        $this->balances[$supplierId] += ($row->credit - $row->debit);

        return [
            $row->date,
            $row->Ref,
            $row->supplier_name,
            $row->credit ?: 0,
            $row->debit ?: 0,
            $this->balances[$supplierId],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER, // Credit
            'E' => NumberFormat::FORMAT_NUMBER, // Debit
            'F' => NumberFormat::FORMAT_NUMBER, // Balance
        ];
    }
}
