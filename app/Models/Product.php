<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'code',
        'Type_barcode',
        'name',
        'cost',
        'price',
        'unit_id',
        'unit_sale_id',
        'unit_purchase_id',
        'stock_alert',
        'current_stock',
        'category_id',
        'sub_category_id',
        'is_variant',
        'is_imei',
        'is_promo',
        'promo_price',
        'promo_start_date',
        'promo_end_date',
        'tax_method',
        'image',
        'brand_id',
        'is_active',
        'note',
        'qty_min'
    ];

    protected $casts = [
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'unit_id' => 'integer',
        'unit_sale_id' => 'integer',
        'unit_purchase_id' => 'integer',
        'is_variant' => 'integer',
        'is_imei' => 'integer',
        'brand_id' => 'integer',
        'is_active' => 'integer',
        'cost' => 'double',
        'price' => 'double',
        'stock_alert' => 'double',
        'current_stock' => 'double',
        'qty_min' => 'double',
        'TaxNet' => 'double',
        'is_promo' => 'integer',
        'promo_price' => 'double',
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function PurchaseDetail()
    {
        return $this->belongsTo('App\Models\PurchaseDetail');
    }

    public function SaleDetail()
    {
        return $this->belongsTo('App\Models\SaleDetail');
    }

    public function QuotationDetail()
    {
        return $this->belongsTo('App\Models\QuotationDetail');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function unitPurchase()
    {
        return $this->belongsTo(Unit::class, 'unit_purchase_id');
    }

    public function unitSale()
    {
        return $this->belongsTo(Unit::class, 'unit_sale_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function ledgers()
    {
        return $this->hasMany(ProductLedger::class);
    }

    public static function booted()
    {
        // static::updated(function ($product) {
        //     self::syncProductQty($product);
        // });
    }

    public static function syncProductQty($product)
    {
        $array_warehouses_id = auth()->user()
            ->assignedWarehouses()
            ->pluck('warehouses.id'); // or just 'id' if no ambiguity

        // dd();

        $product_warehouse = product_warehouse::where('product_id', $product->id)
            ->whereIn('warehouse_id', $array_warehouses_id)
            ->whereNull('deleted_at')
            ->first();

        $product_warehouse->qte = $product->current_stock ?? 0;
        $product_warehouse->save();
    }

    public static function syncProductQtyByReq($product, $request)
    {

        $array_warehouses_id = auth()->user()
            ->assignedWarehouses()
            ->pluck('warehouses.id');

        $product_warehouse = product_warehouse::where('product_id', $product->id)
            ->whereIn('warehouse_id', $array_warehouses_id)
            ->whereNull('deleted_at')
            ->first();

        $product_warehouse->qte = $request['current_stock'];

        $product_warehouse->save();
    }
}
