<?php

namespace App\Models\Temp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseDates extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'temp_database_dates';
}
