<?php

namespace App\Models\DocManagement\Archives;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Kyslik\ColumnSortable\Sortable;

class Transactions extends Model
{
    use HasFactory;
    use HasCompositePrimaryKeyTrait;
    use Compoships;
    use Sortable;

    public $incrementing = false;
    protected $connection = 'archives';
    protected $table = 'transactions';
    protected $primaryKey = ['listingGuid', 'saleGuid'];
    protected $fillable = ['listingGuid', 'saleGuid'];
    public $sortable = [
        'status',
        'address',
        'agent_name',
        'listingDate',
        'actualClosingDate',
        'data_source'
    ];

    // public static function boot() {
    //     parent::boot();
    //     static::addGlobalScope(function ($query) {
    //         $query -> whereHas('docs');
    //     });
    // }



    public function docs() {
        return $this -> hasMany(\App\Models\DocManagement\Archives\Documents::class, ['listingGuid', 'saleGuid'], ['listingGuid', 'saleGuid']);
    }

    public function agent_details() {
        return $this -> hasOne(\App\Models\DocManagement\Archives\Agents::class, 'id', 'agentId');
    }

    public function escrow() {
        return $this -> hasOne(\App\Models\DocManagement\Archives\Escrow::class, ['TransactionId', 'mls'], ['transactionId', 'mlsNumber']);
    }

}
