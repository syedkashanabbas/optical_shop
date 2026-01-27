<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLedger extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'reference',
        'date',
        'quantity_in',
        'quantity_out',
        'balance',
        'product_code',
        'customer_name',
    ];
    

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
