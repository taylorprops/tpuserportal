<?php

namespace App\Models\OldDB\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_company';
    protected $table = 'tbl_loans';
    protected $primaryKey = 'loan_id';
    public $timestamps = false;

}
