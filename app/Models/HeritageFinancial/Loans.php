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

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('duplicate', 'no');
            if (auth() -> user()) {
                if (stristr(auth() -> user() -> group, 'mortgage') && auth() -> user() -> level == 'loan_officer') {
                    $query -> where(function ($query) {
                        $query -> where('loan_officer_1_id', auth() -> user() -> user_id)
                        -> orWhere('loan_officer_2_id', auth() -> user() -> user_id);
                    });
                }
            }

        });
    }

    public function loan_officer_1() {
        return $this -> hasOne(\App\Models\Employees\Mortgage::class, 'id', 'loan_officer_1_id');
    }

    public function loan_officer_2() {
        return $this -> hasOne(\App\Models\Employees\Mortgage::class, 'id', 'loan_officer_2_id');
    }

    public function processor() {
        return $this -> hasOne(\App\Models\Employees\Mortgage::class, 'id', 'processor_id');
    }

    public function lender() {
        return $this -> hasOne(\App\Models\HeritageFinancial\Lenders::class, 'uuid', 'lender_uuid');
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

    public function documents() {
        return $this -> hasMany(\App\Models\HeritageFinancial\LoansDocuments::class, 'loan_uuid', 'uuid');
    }

}
