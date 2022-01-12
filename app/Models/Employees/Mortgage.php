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


    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if (auth() -> user()) {
                if (auth() -> user() -> group == 'mortgage' && auth() -> user() -> level == 'processor') {
                    $query -> where(function ($query) {
                        $query -> where('emp_position', '!=', 'manager');
                    });
                }
            }

        });
    }


    public function notes() {
        return $this -> hasMany('App\Models\Employees\EmployeesNotes', ['emp_id', 'emp_type'], ['id', 'emp_type']);
    }
    public function docs() {
        return $this -> hasMany('App\Models\Employees\EmployeesDocs', ['emp_id', 'emp_type'], ['id', 'emp_type']);
    }
    public function licenses() {
        return $this -> hasMany('App\Models\Employees\EmployeesLicenses', ['emp_id', 'emp_type'], ['id', 'emp_type']);
    }

    public function user() {
        return $this -> hasMany('App\Models\User', ['user_id', 'group'], ['id', 'emp_type']);
    }

}
