<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderLedger extends Model
{
  protected $fillable = [
    'provider_id',
    'type',
    'reference',
    'date',
    'debit',
    'credit',
    'balance'
];


    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
