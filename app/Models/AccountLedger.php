<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountLedger extends Model
{
    protected $fillable = [
        'account_id',
        'type',
        'reference',
        'date',
        'debit',
        'credit',
        'balance',
    ];

    protected $casts = [
        'date' => 'datetime',
        'debit' => 'float',
        'credit' => 'float',
        'balance' => 'float',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class); 
    }
}
