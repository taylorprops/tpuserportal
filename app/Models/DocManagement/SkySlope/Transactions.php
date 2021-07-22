<?php

namespace App\Models\DocManagement\SkySlope;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transactions extends Model
{
    use HasFactory;
    use HasCompositePrimaryKeyTrait;

    public $incrementing = false;
    protected $connection = 'skyslope';
    protected $table = 'transactions';
    protected $primaryKey = ['listingGuid', 'saleGuid'];
    protected $fillable = ['listingGuid', 'saleGuid'];



}
