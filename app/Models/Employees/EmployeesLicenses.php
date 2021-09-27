<?php

namespace App\Models\Employees;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesLicenses extends Model
{
    use HasFactory;
    use Compoships;

    protected $connection = 'mysql';
    protected $table = 'emp_licenses';
    protected $guarded = [];

}
