<?php
namespace App\Exports;

use App\Models\ProductLedger;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductLedgerExport implements FromCollection, WithHeadings
{
    protected $productId;

    public function __construct($productId)
    {
        $this->productId = $productId;
    }

   public function collection()
{
    return ProductLedger::where('product_id', $this->productId)
        ->select(
            'date',
            'type',
            'reference',
            'customer_name',
            'product_code',
            'quantity_in',
            'quantity_out',
            'balance'
        )
        ->orderBy('date')
        ->get();
}

public function headings(): array
{
    return ['Date', 'Type', 'Reference', 'Customer Name', 'Product Code', 'In', 'Out', 'Balance'];
}

}
