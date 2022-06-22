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

    public function save_item(Request $request)
    {

        if ($request -> id) {
            $item = ScheduleChecklist::find($request -> id);
        } else {
            $item = new ScheduleChecklist();
        }

        $states = implode(',', $request -> states);

        $item -> company_id = $request -> company_id;
        $item -> recipient_id = $request -> recipient_id;
        $item -> states = $states;
        $item -> data = $request -> data;

        $item -> save();

        return response() -> json(['status' => 'success']);

    }

}
