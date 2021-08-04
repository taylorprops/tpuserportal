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

    public function docs() {
        return $this -> hasMany(\App\Models\DocManagement\Archives\Documents::class, ['listingGuid', 'saleGuid'], ['listingGuid', 'saleGuid']);
    }



}
