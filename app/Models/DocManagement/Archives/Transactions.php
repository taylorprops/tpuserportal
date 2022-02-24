<?php

namespace App\Models\DocManagement\Archives;

use App\Traits\HasCompositePrimaryKeyTrait;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'data_source',
    ];

    public function docs_sale()
    {
        return $this->hasMany(\App\Models\DocManagement\Archives\Documents::class, 'saleGuid', 'saleGuid')->where(function ($query) {
            $query->where('saleGuid', '!=', '0');
        });
    }

    public function docs_listing()
    {
        return $this->hasMany(\App\Models\DocManagement\Archives\Documents::class, 'listingGuid', 'listingGuid')->where(function ($query) {
            $query->where('listingGuid', '!=', '0');
        });
    }

    public function agent_details()
    {
        return $this->hasOne(\App\Models\DocManagement\Archives\Agents::class, 'id', 'agentId');
    }

    public function escrow()
    {
        return $this->hasOne(\App\Models\DocManagement\Archives\Escrow::class, ['TransactionId', 'mls'], ['transactionId', 'mlsNumber']);
    }
}
