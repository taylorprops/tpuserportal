<?php

namespace App\Http\Controllers\Marketing\Schedule;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Marketing\Schedule\Schedule;
use App\Models\Marketing\Schedule\ScheduleUploads;
use App\Models\Marketing\Schedule\ScheduleSettings;
use App\Models\DocManagement\Resources\LocationData;

class ScheduleController extends Controller
{

    public function schedule(Request $request) {

        $settings = ScheduleSettings::orderBy('item') -> get();
        $states = LocationData::activeStates();

        return view('marketing/schedule/schedule', compact('settings', 'states'));

    }

    public function get_schedule(Request $request) {

        $events = Schedule::where('active', TRUE)
        -> with(['company', 'medium', 'recipient', 'uploads'])
        -> orderBy('event_date', 'desc')
        -> get();

        return view('marketing/schedule/get_schedule_html', compact('events'));

    }

    public function save_item(Request $request) {

        $validated = $request -> validate([
            'event_date' => 'required',
            'recipient_id' => 'required',
            'states' => 'required|numeric|min:0|not_in:0',
            'company_id' => 'required',
            'medium_id' => 'required',
            'description' => 'required',
        ],
        [
            'required' => 'Required',
            'states.not_in' => 'State is required',
        ]);

        if(!$request -> id) {

            $validated = $request -> validate([
                'upload_html' => 'required_without:upload_file',
                'upload_file' => 'required_without:upload_html',
            ],
            [
                'upload_html.required_without' => 'You must paste HTML or upload a file',
                'upload_file.required_without' => 'You must paste HTML or upload a file',
            ]);

        }

        $id = $request -> id;
        $event_date = $request -> event_date;
        $recipient_id = $request -> recipient_id;
        $state = implode(',', $request -> state);
        $company_id = $request -> company_id;
        $medium_id = $request -> medium_id;
        $description = $request -> description;
        $upload_html = $request -> upload_html;
        $upload_file = $request -> file('upload_file');

        $company = ScheduleSettings::where('id', $company_id) -> first();
        $company = $company -> item;
        $recipient = ScheduleSettings::where('id', $recipient_id) -> first();
        $recipient = $recipient -> item;

        $uuid = date("Y", strtotime($event_date)).'-'.date("m", strtotime($event_date)).'-'.Helper::get_initials($company).'-'.$recipient;
        dd($uuid);


        if($id) {
            $event = Schedule::find($id);
        } else {
            $event = new Schedule();
        }

        $event -> event_date = $event_date;
        $event -> recipient_id = $recipient_id;
        $event -> state = $state;
        $event -> company_id = $company_id;
        $event -> medium_id = $medium_id;
        $event -> description = $description;
        $event -> uuid = $uuid;

        $event -> save();
        $event_id = $event -> id;

        if($upload_file) {

            $upload_file_name = Helper::clean_file_name($upload_file, '', false, true);
            $ext = $upload_file -> getClientOriginalExtension();
            $upload_file_type = strtolower($ext) == 'pdf' ? 'pdf' : 'image';

            $dir = 'marketing/'.$event_id;
            if (! is_dir($dir)) {
                Storage::makeDirectory($dir);
            }
            $upload_file -> storeAs($dir, $upload_file_name);
            $upload_file_location = $dir.'/'.$upload_file_name;
            $upload_file_url = Storage::url($dir.'/'.$upload_file_name);

            $upload = new ScheduleUploads();
            $upload -> file_name = $upload_file_name;
            $upload -> file_type = $upload_file_type;
            $upload -> file_location = $upload_file_location;
            $upload -> file_url = $upload_file_url;
            $upload -> event_id = $event_id;
            $upload -> save();

        }

        if($upload_html) {

            $upload = new ScheduleUploads();
            $upload -> html = $upload_html;
            $upload -> event_id = $event_id;
            $upload -> save();

        }




    }

    public function show_versions(Request $request) {

        $event_id = $request -> id;
        $versions = ScheduleUploads::where('event_id', $event_id) -> get();

        return view('marketing/schedule/get_versions_html', compact('event_id', 'versions'));

    }


    public function schedule_settings(Request $request) {

        $settings = ScheduleSettings::orderBy('item') -> get();
        $categories = [];
        foreach($settings as $setting) {
            $categories[] = $setting -> category;
        }
        $categories = array_unique($categories);

        return view('marketing/schedule/schedule_settings', compact('categories'));

    }

    public function get_schedule_settings(Request $request) {

        $settings = ScheduleSettings::orderBy('item') -> get();

        $categories = [];
        foreach($settings as $setting) {
            $categories[] = $setting -> category;
        }
        $categories = array_unique($categories);

        $items = [];
        foreach($categories as $category) {
            $data['category'] = $category;
            $details = [];
            foreach($settings -> where('category', $category) as $setting) {
                $details[] = [
                    'id' => $setting -> id,
                    'item' => $setting -> item
                ];
            }
            $data['details'] = $details;
            array_push($items, $data);
        }

        $items = json_encode($items);

        return $items;

    }

    public function settings_get_reassign_options(Request $request) {

        $category = $request -> category;
        $id = $request -> id;

        $in_use = Schedule::where($category.'_id', $id) -> first();

        if(!$in_use) {
            ScheduleSettings::find($id) -> delete();
            return response() -> json(['deleted' => true]);
        }

        $settings = ScheduleRecipients::where('id', '!=', $id) -> where('category', $category) -> orderBy('recipient') -> get();

        return compact('settings');

    }

    public function settings_save_add_item(Request $request) {

        $category = $request -> type;
        $value = $request -> value;

        ScheduleSettings::create([
            'category' => $category,
            'item' => $value,
        ]);

        return response() -> json(['status' => 'success']);

    }

    public function settings_save_edit_item(Request $request) {

        $id = $request -> id;
        $value = $request -> value;

        ScheduleSettings::find($id) -> update([
            'item' => $value
        ]);

        return response() -> json(['status' => 'success']);

    }

    public function settings_save_delete_item(Request $request) {

        dd($request -> all());

    }




}
