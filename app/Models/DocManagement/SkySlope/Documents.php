<?php

namespace App\Models\DocManagement\SkySlope;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $connection = 'skyslope';
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $fillable = ['id'];

}
