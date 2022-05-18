<?php

namespace App\Models\Marketing\Schedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'marketing_schedule';
    protected $guarded = [];


    public function notes()
    {
        return $this -> hasMany(\App\Models\Marketing\Schedule\ScheduleNotes::class, 'event_id', 'id') -> orderBy('created_at', 'desc');
    }

    public function medium()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleSettings::class, 'id', 'medium_id');
    }

    public function company()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleSettings::class, 'id', 'company_id');
    }

    public function recipient()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleSettings::class, 'id', 'recipient_id');
    }

    public function focus()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleSettings::class, 'id', 'focus_id');
    }

    public function goal()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleSettings::class, 'id', 'goal_id');
    }

    public function status()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleSettings::class, 'id', 'status_id');
    }

    public function uploads()
    {
        return $this -> hasMany(\App\Models\Marketing\Schedule\ScheduleUploads::class, 'event_id', 'id') -> orderBy('created_at', 'desc');
    }





}
