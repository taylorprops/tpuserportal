<?php

namespace App\Models\Marketing\Schedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleNotes extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'marketing_schedule_notes';
    protected $guarded = [];

    public function user()
    {
        return $this -> hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    public function event()
    {
        return $this -> belongsTo(\App\Models\Marketing\Schedule\Schedule::class, 'id', 'event_id');
    }

}
