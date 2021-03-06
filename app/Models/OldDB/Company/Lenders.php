<?php

namespace App\Models\OldDB\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lenders extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_company';

    protected $table = 'tbl_lenders';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
