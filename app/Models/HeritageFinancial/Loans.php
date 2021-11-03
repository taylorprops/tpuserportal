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

    public function loan_officer_1() {
        return $this -> hasOne(\App\Models\Employees\LoanOfficers::class, 'id', 'loan_officer_1_id');
    }

    public function loan_officer_2() {
        return $this -> hasOne(\App\Models\Employees\LoanOfficers::class, 'id', 'loan_officer_2_id');
    }

    public function processor() {
        return $this -> hasOne(\App\Models\Employees\LoanOfficers::class, 'id', 'processor_id');
    }

    public function deductions() {
        return $this -> hasMany(\App\Models\HeritageFinancial\LoansDeductions::class, 'loan_uuid', 'uuid');
    }

    public function checks_in() {
        return $this -> hasMany(\App\Models\HeritageFinancial\LoansChecksIn::class, 'loan_uuid', 'uuid');
    }

    public function loan_officer_deductions() {
        return $this -> hasMany(\App\Models\HeritageFinancial\LoansLoanOfficerDeductions::class, 'loan_uuid', 'uuid');
    }

}
