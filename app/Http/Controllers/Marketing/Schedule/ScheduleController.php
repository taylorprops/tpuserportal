<?php

namespace App\Http\Controllers\Marketing\Schedule;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $company_id = $request -> company_id;
        $recipient_id = $request -> recipient_id;
        $medium_id = $request -> medium_id;

        $events = Schedule::where('active', TRUE)
        -> where(function($query) use ($company_id, $recipient_id, $medium_id) {
            if($company_id) {
                $query -> where('company_id', $company_id);
            }
            if($recipient_id) {
                $query -> where('recipient_id', $recipient_id);
            }
            if($medium_id) {
                $query -> where('medium_id', $medium_id);
            }
        })
        -> with(['company', 'medium', 'recipient', 'uploads' => function($query) {
            $query -> where('active', TRUE);
        }])
        -> orderBy('event_date', 'desc')
        -> get();

        if($events) {

            return view('marketing/schedule/get_schedule_html', compact('events'));

        }

        return response() -> json(['status' => 'no event']);

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
        $subject_line_a = $request -> subject_line_a;
        $subject_line_b = $request -> subject_line_b;
        $preview_text = $request -> preview_text;

        $company = ScheduleSettings::where('id', $company_id) -> first();
        $company = $company -> item;
        $recipient = ScheduleSettings::where('id', $recipient_id) -> first();
        $recipient = $recipient -> item;

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
        $event -> subject_line_a = $subject_line_a;
        $event -> subject_line_b = $subject_line_b;
        $event -> preview_text = $preview_text;

        $event -> save();
        $event_id = $event -> id;

        $uuid = date("Y", strtotime($event_date)).'-'.date("m", strtotime($event_date)).'_'.Helper::get_initials($company).'_'.$event_id;
        $event -> uuid = $uuid;
        $event -> save();

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
        $versions = ScheduleUploads::where('event_id', $event_id) -> where('active', TRUE) -> get();

        return view('marketing/schedule/get_versions_html', compact('event_id', 'versions'));

    }

    public function save_add_version(Request $request) {

        $validated = $request -> validate([
            'upload_version_html' => 'required_without:upload_version_file',
            'upload_version_file' => 'required_without:upload_version_html',
        ],
        [
            'upload_version_html.required_without' => 'You must paste HTML or upload a file',
            'upload_version_file.required_without' => 'You must paste HTML or upload a file',
        ]);

        $event_id = $request -> event_id;

        $upload_file = $request -> file('upload_version_file');
        $upload_html = $request -> upload_version_html;

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
            $upload -> accepted_version = false;
            $upload -> save();

        }

        if($upload_html) {

            $upload = new ScheduleUploads();
            $upload -> html = $upload_html;
            $upload -> event_id = $event_id;
            $upload -> save();

        }

        return response() -> json(['status' => 'success']);

    }

    public function delete_version(Request $request) {

        $event_id = $request -> event_id;
        $version_id = $request -> version_id;

        $delete = ScheduleUploads::find($version_id) -> update([
            'active' => false
        ]);

        return response() -> json(['status' => 'success']);
    }

    public function calendar_get_events(Request $request) {

        $company_id = $request -> company_id;
        $recipient_id = $request -> recipient_id;
        $medium_id = $request -> medium_id;

        $events = Schedule::select(['id', 'company_id', 'recipient_id', DB::raw('SUBSTRING(uuid, 9) as title'), 'event_date as start']) -> where('active', TRUE)
        -> where(function($query) use ($company_id, $recipient_id, $medium_id) {
            if($company_id) {
                $query -> where('company_id', $company_id);
            }
            if($recipient_id) {
                $query -> where('recipient_id', $recipient_id);
            }
            if($medium_id) {
                $query -> where('medium_id', $medium_id);
            }
        })
        -> with(['company', 'recipient'])
        -> orderBy('event_date', 'desc')
        -> get();

        foreach($events as $event) {
            $event -> className = ['bg-'.$event -> company -> color.'-600', 'border-'.$event -> company -> color.'-200', 'rounded', 'flex', 'items-center'];
            $event -> title = $event -> title.'-'.$event -> recipient -> item;
        }

        return response() -> json($events);

    }

    public function delete_event(Request $request) {

        $delete = Schedule::find($request -> id) -> update([
            'active' => 0
        ]);

        return response() -> json(['status' => 'success']);

    }

    public function clone_event(Request $request) {

        $event_id = $request -> id;

        $event = Schedule::find($event_id);
        $clone = $event -> replicate();
        $clone -> save();
        $clone_id = $clone -> id;

        $uploads = ScheduleUploads::where('event_id', $event_id) -> get();

        foreach($uploads as $upload) {

            $upload_clone = $upload -> replicate();
            $upload_clone -> event_id = $clone_id;

            if($upload -> html == '') {
                $dir = 'marketing/'.$clone_id;
                if (! is_dir($dir)) {
                    Storage::makeDirectory($dir);
                }

                $upload_file_name = basename(Storage::path($upload -> file_location));

                Storage::copy($upload -> file_location, $dir.'/'.$upload_file_name);

                $upload_file_location = $dir.'/'.$upload_file_name;
                $upload_file_url = Storage::url($dir.'/'.$upload_file_name);

                $upload_clone -> file_location = $upload_file_location;
                $upload_clone -> file_url = $upload_file_url;
            }

            $upload_clone -> save();
        }

        return response() -> json(['id' => $clone_id]);

    }


    public function schedule_settings(Request $request) {

        return view('marketing/schedule/schedule_settings');

    }

    public function get_schedule_settings(Request $request) {

        $settings = ScheduleSettings::orderBy('item') -> get();

        $categories = [];
        foreach($settings as $setting) {
            $categories[] = $setting -> category;
        }
        $categories = array_unique($categories);

        return view('marketing/schedule/get_schedule_settings_html', compact('categories', 'settings'));

    }

    public function settings_get_reassign_options(Request $request) {

        $category = $request -> category;
        $id = $request -> id;

        $in_use = Schedule::where($category.'_id', $id) -> first();

        if(!$in_use) {
            ScheduleSettings::find($id) -> delete();
            return response() -> json(['deleted' => true]);
        }

        $settings = ScheduleSettings::where('id', '!=', $id) -> where('category', $category) -> orderBy('item') -> get();

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
        $field = $request -> field;

        ScheduleSettings::find($id) -> update([
            $field => $value
        ]);

        return response() -> json(['status' => 'success']);

    }


    public function settings_reassign_items(Request $request) {

        $new_setting_id = $request -> new_setting_id;
        $deleted_setting_id = $request -> deleted_setting_id;
        $category = $request -> category;

        $reassign = Schedule::where($category.'_id', $deleted_setting_id) -> update([
            $category.'_id' => $new_setting_id
        ]);

        ScheduleSettings::find($deleted_setting_id) -> delete();

        return response() -> json(['status' => 'success']);

    }




}
