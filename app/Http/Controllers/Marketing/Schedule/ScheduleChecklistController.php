<?php

namespace App\Http\Controllers\Marketing\Schedule;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Schedule\ScheduleChecklist;
use App\Models\Marketing\Schedule\ScheduleSettings;
use Illuminate\Http\Request;

class ScheduleChecklistController extends Controller
{
    public function checklist(Request $request)
    {

        $settings = ScheduleSettings::whereIn('category', ['company', 'recipient']) -> orderBy('order') -> get();

        return view('/marketing/schedule/checklist', compact('settings'));

    }

    public function get_checklist(Request $request)
    {

        $checklist = ScheduleChecklist::get();
        $settings = ScheduleSettings::whereIn('category', ['company', 'recipient']) -> orderBy('order') -> get();

        return view('/marketing/schedule/get_checklist_html', compact('checklist', 'settings'));

    }
}
