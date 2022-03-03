<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCoordinators extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'emp_transaction_coordinators';

    protected $guarded = [];

    public function user()
    {
        return $this->hasMany(\App\Models\User::class, ['user_id', 'group'], ['id', 'emp_type']);
    }
}
