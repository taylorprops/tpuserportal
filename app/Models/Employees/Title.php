<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'emp_title';

    protected $guarded = [];

    public function user()
    {
        return $this->hasMany('App\Models\User', ['user_id', 'group'], ['id', 'emp_type']);
    }
}
