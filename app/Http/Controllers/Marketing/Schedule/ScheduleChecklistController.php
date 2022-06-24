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

        $checklist = ScheduleChecklist::where('active', true) -> orderBy('order') -> get();
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

        $recipient_ids = implode(',', $request -> recipient_ids);
        $states = implode(',', $request -> states);

        $item -> company_id = $request -> company_id;
        $item -> recipient_ids = $recipient_ids;
        $item -> states = $states;
        $item -> data = $request -> data;

        $item -> save();

        return response() -> json(['status' => 'success']);

    }

    public function delete_item(Request $request)
    {

        ScheduleChecklist::find($request -> id) -> update([
            'active' => false,
        ]);

    }

    public function update_order(Request $request)
    {
        foreach (json_decode($request -> items, true) as $key => $value) {
            dump($value['id'], $value['order']);
            ScheduleChecklist::find($value['id'])
                -> update([
                    'order' => $value['order'],
                ]);
        }
    }

}
