<?php

namespace App\Models\HeritageFinancial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LendersDocuments extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'heritage_financial_lenders_documents';

    protected $guarded = [];
}
