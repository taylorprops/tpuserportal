<?php

namespace App\Models\DocManagement\Archives;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transactions extends Model
{
    use HasFactory;
    use HasCompositePrimaryKeyTrait;
    use Compoships;

    public $incrementing = false;
    protected $connection = 'archives';
    protected $table = 'transactions';
    protected $primaryKey = ['listingGuid', 'saleGuid'];
    protected $fillable = ['listingGuid', 'saleGuid'];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> whereHas('docs');
        });
    }



    public function docs() {
        return $this -> hasMany(\App\Models\DocManagement\Archives\Documents::class, ['listingGuid', 'saleGuid'], ['listingGuid', 'saleGuid']);
    }

    public function agent_details() {
        return $this -> hasOne(\App\Models\DocManagement\Archives\Agents::class, 'id', 'agentId');
    }



}
