<?php

namespace App\Models\Marketing\Schedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleChecklist extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'marketing_schedule_checklist';
    protected $guarded = [];


    public function company()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleSettings::class, 'id', 'company_id');
    }

    public function recipient()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleSettings::class, 'id', 'recipient_id');
    }

    public function section()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleSettings::class, 'id', 'section_id');
    }

}
