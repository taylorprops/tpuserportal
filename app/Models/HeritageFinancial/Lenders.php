<?php

namespace App\Models\HeritageFinancial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lenders extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'heritage_financial_lenders';
    protected $guarded = [];

    public function notes() {
        return $this -> hasMany(\App\Models\HeritageFinancial\LendersNotes::class, 'lender_id', 'id');
    }

    public function documents() {
        return $this -> hasMany(\App\Models\HeritageFinancial\LendersDocuments::class, 'lender_id', 'id');
    }

}
