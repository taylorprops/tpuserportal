<?php

namespace App\Models\Employees;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mortgage extends Model
{
    use HasFactory;
    use Compoships;

    protected $connection = 'mysql';

    protected $table = 'emp_mortgage';

    protected $guarded = [];

    public function notes()
    {
        return $this->hasMany(\App\Models\Employees\EmployeesNotes::class, ['emp_id', 'emp_type'], ['id', 'emp_type']);
    }

    public function docs()
    {
        return $this->hasMany(\App\Models\Employees\EmployeesDocs::class, ['emp_id', 'emp_type'], ['id', 'emp_type']);
    }

    public function licenses()
    {
        return $this->hasMany(\App\Models\Employees\EmployeesLicenses::class, ['emp_id', 'emp_type'], ['id', 'emp_type']);
    }

    public function loans()
    {
        return $this->hasMany(\App\Models\HeritageFinancial\Loans::class, 'loan_officer_1_id', 'id');
    }

    public function user()
    {
        return $this->hasMany(\App\Models\User::class, ['user_id', 'group'], ['id', 'emp_type']);
    }
}
