<?php

namespace App\Http\Controllers\Marketing\Schedule;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Marketing\Schedule\ScheduleSettings;
use App\Models\Marketing\Schedule\ScheduleChecklist;

class ScheduleChecklistController extends Controller
{
    public function checklist(Request $request) {

        return view('/marketing/schedule/checklist');

    }

    public function get_checklist(Request $request) {

        $checklist = ScheduleChecklist::get();
        $settings = ScheduleSettings::whereIn('category', ['company', 'recipient']) -> orderBy('order') -> get();

        return view('/marketing/schedule/get_checklist_html', compact('checklist', 'settings'));

    }
}
