<?php

namespace App\Models\OldDB\SkySlope;

use App\Traits\HasCompositePrimaryKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listings extends Model
{
    use HasFactory;
    use HasCompositePrimaryKeyTrait;

    protected $connection = 'mysql_old_skyslope';

    protected $table = 'listings';

    protected $primaryKey = ['TransactionId', 'ListingId'];

    public $timestamps = false;

    protected $fillable = ['TransactionId', 'ListingId'];
    //protected $guarded = [];
}
