<?php

namespace App\Models\Employees;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeesDocs extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Compoships;

    protected $connection = 'mysql';
    protected $table = 'emp_docs';
    protected $guarded = [];

}
