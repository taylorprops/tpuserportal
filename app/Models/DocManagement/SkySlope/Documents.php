<?php

namespace App\Models\DocManagement\SkySlope;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Documents extends Model
{
    use HasFactory;
    use Compoships;

    public $incrementing = false;
    protected $connection = 'skyslope';
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $fillable = ['id'];



}
