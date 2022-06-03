<?php

namespace App\Models\Notes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'notes';
    protected $guarded = [];

    public function user()
    {
        return $this -> hasOne(\App\Models\User::class, 'id', 'user_id');
    }

}
