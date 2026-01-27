<?php 

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductLedger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductLedgerExport;

class ProductLedgerController extends Controller
{
    public function index($productId)
    {
        $product = Product::with('ledgers')->findOrFail($productId);
        return view('product-ledger.index', compact('product'));
    }

    
    public function downloadExcel($productId)
    {
        return Excel::download(new ProductLedgerExport($productId), 'product-ledger-'.$productId.'.xlsx');
    }
    
}
