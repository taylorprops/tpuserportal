<?php

namespace App\Models\Employees;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InHouse extends Model
{
    use HasFactory;
    use Compoships;

    protected $connection = 'mysql';
    protected $table = 'emp_in_house';
    protected $guarded = [];

    public function notes() {
        return $this -> hasMany('App\Models\Employees\EmployeesNotes', ['emp_id', 'emp_type'], ['id', 'emp_type']);
    }
    public function docs() {
        return $this -> hasMany('App\Models\Employees\EmployeesDocs', ['emp_id', 'emp_type'], ['id', 'emp_type']);
    }

    public function user() {
        return $this -> hasMany('App\Models\User', ['user_id', 'group'], ['id', 'emp_type']);
    }

}
