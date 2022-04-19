<?php

namespace App\Http\Controllers\Marketing\Schedule;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Marketing\Schedule\Schedule;
use App\Models\Marketing\Schedule\ScheduleMediums;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\Marketing\Schedule\ScheduleCategories;

class ScheduleController extends Controller
{

    public function schedule(Request $request) {

        $categories = ScheduleCategories::orderBy('category') -> get();
        $mediums = ScheduleMediums::with(['descriptions']) -> orderBy('medium') -> get();
        $states = LocationData::activeStates();

        return view('marketing/schedule/schedule', compact('categories', 'mediums', 'states'));

    }

    public function get_schedule(Request $request) {

        $items = Schedule::orderBy('deploy_date', 'desc') -> get();


        return view('marketing/schedule/get_schedule_html', compact('items'));

    }

    public function schedule_settings(Request $request) {

        return view('marketing/schedule/schedule_settings');

    }

    public function get_schedule_settings(Request $request) {

        $field = $request -> field;

        if($field == 'categories') {
            $settings = ScheduleCategories::orderBy('category') -> get();
        } else if($field == 'mediums') {
            $settings = ScheduleMediums::with(['descriptions']) -> orderBy('medium') -> get();
        }

        return view('marketing/schedule/get_schedule_settings_html', compact('field', 'settings'));

    }


}
