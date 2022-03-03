<?php

namespace App\Models\HeritageFinancial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoansLoanOfficerDeductions extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'heritage_financial_loans_loan_officer_deductions';

    protected $guarded = [];
}
