<?php

namespace App\Models\Marketing\Schedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleCategories extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'marketing_schedule_categories';
    protected $guarded = [];
    public $timestamps = false;

}
