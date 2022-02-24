<?php

namespace App\Models\OldDB\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_company';

    protected $table = 'mls_company';

    protected $primaryKey = 'ListingSourceRecordKey';

    protected $fillable = ['ListingSourceRecordKey', 'ListingSourceRecordId', 'downloaded'];

    public $timestamps = false;

    public function docs()
    {
        return $this->hasMany(\App\Models\OldDB\Company\Documents::class, 'upload_mls', 'ListingSourceRecordId');
    }
}
