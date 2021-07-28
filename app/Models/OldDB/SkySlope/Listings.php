<?php

namespace App\Models\OldDB\SkySlope;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Listings extends Model
{
    use HasFactory;
    use HasCompositePrimaryKeyTrait;

    protected $connection = 'mysql_old_skyslope';
    protected $table = 'listings_test';
    protected $primaryKey = ['TransactionId', 'ListingId'];
    public $timestamps = false;
    protected $fillable = ['TransactionId', 'ListingId'];

}