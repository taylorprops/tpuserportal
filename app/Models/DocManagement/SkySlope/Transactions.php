<?php

namespace App\Models\DocManagement\SkySlope;

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
    protected $connection = 'skyslope';
    protected $table = 'transactions';
    protected $primaryKey = ['listingGuid', 'saleGuid'];
    protected $fillable = ['listingGuid', 'saleGuid'];

    public function docs() {
        return $this -> hasMany(\App\Models\DocManagement\SkySlope\Documents::class, ['listingGuid', 'saleGuid'], ['listingGuid', 'saleGuid']);
    }



}
