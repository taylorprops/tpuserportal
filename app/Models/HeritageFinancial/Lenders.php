<?php

namespace App\Models\HeritageFinancial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lenders extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'heritage_financial_lenders';
    protected $guarded = [];

    public function notes() {
        return $this -> hasMany(\App\Models\HeritageFinancial\LendersNotes::class, 'lender_id', 'id');
    }

}
