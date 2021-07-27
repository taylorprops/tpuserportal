<?php

namespace App\Models\OldDB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_company';
    protected $table = 'tbl_agents';
    protected $primaryKey = 'id';

}
