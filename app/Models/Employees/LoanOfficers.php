<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanOfficers extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'emp_loan_officers';
    protected $guarded = [];

    public function notes() {
        return $this -> hasMany('App\Models\Employees\LoanOfficersNotes', 'emp_loan_officers_id', 'id');
    }
    public function docs() {
        return $this -> hasMany('App\Models\Employees\LoanOfficersDocs', 'emp_loan_officers_id', 'id');
    }
    public function licenses() {
        return $this -> hasMany('App\Models\Employees\LoanOfficersLicenses', 'emp_loan_officers_id', 'id');
    }

}
