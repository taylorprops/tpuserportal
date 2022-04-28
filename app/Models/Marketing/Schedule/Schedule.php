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

    public function uploads()
    {
        return $this -> hasMany(\App\Models\Marketing\Schedule\ScheduleUploads::class, 'event_id', 'id');
    }



}
