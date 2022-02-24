<?php

namespace App\Models\DocManagement\Archives;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $connection = 'archives';

    protected $table = 'users';

    protected $primaryKey = 'userGuid';

    protected $fillable = ['userGuid'];
}
