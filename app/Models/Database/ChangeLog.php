<?php

namespace App\Models\Database;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'database_change_log';
    protected $guarded = [];


    public function changes() {
        return $this -> hasMany(\App\Models\Database\DatabaseChangeLogChanges::class, 'change_id', 'id');
    }

}
