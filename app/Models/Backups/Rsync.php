<?php

namespace App\Models\Backups;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rsync extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'backups_rsync';

}
