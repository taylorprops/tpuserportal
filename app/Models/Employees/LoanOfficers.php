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

}
