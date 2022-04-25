<?php

namespace App\Models\Marketing\Schedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleMediumDescriptions extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'marketing_schedule_medium_descriptions';
    protected $guarded = [];
    public $timestamps = false;

}
