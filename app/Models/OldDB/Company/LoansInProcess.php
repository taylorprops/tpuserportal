<?php

namespace App\Models\OldDB\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoansInProcess extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_company';

    protected $table = 'tbl_loans_in_process';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
