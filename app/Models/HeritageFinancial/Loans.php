<?php

namespace App\Models\HeritageFinancial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'heritage_financial_loans';
    protected $guarded = [];

    public function deductions() {
        return $this -> hasMany(\App\Models\HeritageFinancial\LoansDeductions::class, 'loan_uuid', 'uuid');
    }

}
