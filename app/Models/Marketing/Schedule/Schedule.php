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
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleMediums::class, 'id', 'medium_id');
    }

    public function company()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleCompanies::class, 'id', 'company_id');
    }

    public function recipient()
    {
        return $this -> hasOne(\App\Models\Marketing\Schedule\ScheduleRecipients::class, 'id', 'recipient_id');
    }



}
