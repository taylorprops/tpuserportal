<?php

namespace App\Models\Marketing\Schedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleMediums extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'marketing_schedule_mediums';
    protected $guarded = [];

    public function descriptions()
    {
        return $this -> hasMany(\App\Models\Marketing\Schedule\ScheduleMediumDescriptions::class, 'medium_id', 'id');
    }

}
