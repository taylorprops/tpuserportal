<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanOfficersDocs extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'emp_loan_officers_docs';
    protected $guarded = [];

}
