<?php

namespace App\Models\OldDB\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escrow extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_company';
    protected $table = 'escrow';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
