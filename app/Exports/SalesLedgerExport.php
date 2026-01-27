<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\PaymentSale;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class SalesLedgerExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $balances = [];
    protected $start_date;
    protected $end_date;
    protected $Ref;
    protected $payment_status;
    protected $client_id;
    protected $warehouse_id;

    public function __construct($filters = [])
    {
        $this->start_date = $filters['start_date'] ?? null;
        $this->end_date = $filters['end_date'] ?? null;
        $this->Ref = $filters['Ref'] ?? null;
        $this->payment_status = $filters['payment_status'] ?? null;
        $this->client_id = $filters['client_id'] ?? null;
        $this->warehouse_id = $filters['warehouse_id'] ?? null;
    }

    public function collection()
    {
        // SALES → now DEBIT (GrandTotal minus returns)
        $saleRows = Sale::with('client')
            ->when($this->start_date, fn($q) => $q->whereDate('date', '>=', $this->start_date))
            ->when($this->end_date, fn($q) => $q->whereDate('date', '<=', $this->end_date))
            ->when($this->Ref, fn($q) => $q->where('Ref', 'like', "%{$this->Ref}%"))
            ->when($this->client_id, fn($q) => $q->where('client_id', $this->client_id))
            ->when($this->warehouse_id, fn($q) => $q->where('warehouse_id', $this->warehouse_id))
            ->when($this->payment_status, function($q) {
                if ($this->payment_status == 'paid') $q->where('payment_statut','paid');
                if ($this->payment_status == 'partial') $q->where('payment_statut','partial');
                if ($this->payment_status == 'unpaid') $q->where('payment_statut','unpaid');
            })
            ->get()
            ->map(function ($sale) {
                return (object) [
                    'date' => $sale->date,
                    'Ref' => $sale->Ref,
                    'client_id' => $sale->client_id,
                    'client_name' => optional($sale->client)->username ?? optional($sale->client)->name ?? 'Unknown',
                    'debit' => max((float) $sale->GrandTotal - (float) $sale->total_retturn, 0), // now DEBIT
                    'credit' => 0,
                ];
            });

        // PAYMENTS → now CREDIT
        $paymentRows = PaymentSale::with('sale.client')
            ->when($this->start_date, fn($q) => $q->whereDate('date', '>=', $this->start_date))
            ->when($this->end_date, fn($q) => $q->whereDate('date', '<=', $this->end_date))
            ->when($this->Ref, fn($q) => $q->where('Ref', 'like', "%{$this->Ref}%"))
            ->when($this->client_id, fn($q) => $q->whereHas('sale', fn($q2) => $q2->where('client_id', $this->client_id)))
            ->when($this->warehouse_id, fn($q) => $q->whereHas('sale', fn($q2) => $q2->where('warehouse_id', $this->warehouse_id)))
            ->get()
            ->map(function ($payment) {
                return (object) [
                    'date' => $payment->date,
                    'Ref' => $payment->Ref,
                    'client_id' => optional($payment->sale)->client_id,
                    'client_name' => optional($payment->sale->client)->username ?? optional($payment->sale->client)->name ?? 'Unknown',
                    'debit' => 0,
                    'credit' => (float) $payment->montant, // now CREDIT
                ];
            });

        // Merge sales and payments, sort by date
        return $saleRows->merge($paymentRows)->sortBy('date')->values();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Ref',
            'Client',
            'Debit',
            'Credit',
            'Balance',
        ];
    }

    public function map($row): array
    {
        $clientId = $row->client_id;
        if (!isset($this->balances[$clientId])) {
            $this->balances[$clientId] = 0;
        }

        // Running balance = old balance + debit – credit
        $this->balances[$clientId] += ($row->debit - $row->credit);

        return [
            $row->date,
            $row->Ref,
            $row->client_name,
            $row->debit ?: 0,
            $row->credit ?: 0,
            $this->balances[$clientId],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER, // Debit
            'E' => NumberFormat::FORMAT_NUMBER, // Credit
            'F' => NumberFormat::FORMAT_NUMBER, // Balance
        ];
    }
}
