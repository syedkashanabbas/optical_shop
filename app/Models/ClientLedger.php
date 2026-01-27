<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientLedger extends Model
{
    protected $table = 'clients_ledgers';

    protected $fillable = [
        'client_id',
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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
