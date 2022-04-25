<?php

namespace App\Models\Marketing\Schedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleCompanies extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'marketing_schedule_companies';
    protected $guarded = [];
    public $timestamps = false;

}
