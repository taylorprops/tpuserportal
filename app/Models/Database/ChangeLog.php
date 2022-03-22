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

    public function changes()
    {
        return $this -> hasMany(\App\Models\Database\ChangeLogChanges::class, 'change_id', 'id');
    }

    public function user()
    {
        return $this -> hasOne(\App\Models\User::class, 'id', 'changed_by');
    }
}
