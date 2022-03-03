<?php

namespace App\Models\DocManagement\Transactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'docs_transactions';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if (auth()->user()) {
                if (stristr(auth()->user()->group, 'agent')) {
                    $query->where(function ($query) {
                        $query->where('Agent_ID', auth()->user()->user_id)
                        ->orWhere('CoAgent_ID', auth()->user()->user_id);
                    });
                } elseif (auth()->user()->group == 'transaction_coordinator') {
                    $query->where('TransactionCoordinator_ID', auth()->user()->user_id);
                }
                //$query -> where('Status', '>', '0');
            }
        });
    }
}
