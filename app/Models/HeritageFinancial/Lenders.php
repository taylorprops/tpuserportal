<?php

namespace App\Models\HeritageFinancial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lenders extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'heritage_financial_lenders';

    protected $guarded = [];

    public function documents()
    {
        return $this->hasMany(\App\Models\HeritageFinancial\LendersDocuments::class, 'lender_uuid', 'uuid');
    }
}
