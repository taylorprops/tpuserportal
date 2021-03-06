<?php

namespace App\Models\DocManagement\Archives;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escrow extends Model
{
    use HasFactory;
    use Compoships;

    protected $connection = 'archives';

    protected $table = 'escrow';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public $sortable = [
        'address',
        'agent',
        'contract_date',
    ];

    public function checks()
    {
        return $this->hasMany(\App\Models\DocManagement\Archives\EscrowChecks::class, 'escrow_id', 'id');
    }

    public function transaction_skyslope()
    {
        return $this->belongsTo(\App\Models\DocManagement\Archives\Transactions::class, 'TransactionId', 'transactionId')->where(function ($query) {
            $query->where('TransactionId', '>', '0');
        });
    }

    public function transaction_company()
    {
        return $this->belongsTo(\App\Models\DocManagement\Archives\Transactions::class, 'mls', 'mlsNumber');
    }
}
