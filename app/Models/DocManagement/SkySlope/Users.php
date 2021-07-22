<?php

namespace App\Models\DocManagement\SkySlope;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $connection = 'skyslope';
    protected $table = 'users';
    protected $primaryKey = 'userGuid';
    protected $fillable = ['userGuid'];

}
