<?php

namespace App\Models\Employees;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesNotes extends Model
{
    use HasFactory;
    use Compoships;

    protected $connection = 'mysql';

    protected $table = 'emp_notes';

    protected $guarded = [];

    public function user()
    {
        return $this -> hasOne(\App\Models\User::class, 'id', 'user_id');
    }

}
