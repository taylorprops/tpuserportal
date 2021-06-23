<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'config';
    protected $fillable = ['config_key', 'config_value', 'value_type'];
    public $timestamps = false;

}
