<?php

namespace App\Http\Controllers\Marketing\Data;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BrightMLS\BrightOffices;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DocManagement\Resources\LocationData;

class DataController extends Controller
{

    private $agent_columns = ['MemberFullName', 'MemberFirstName', 'MemberLastName','MemberEmail', 'MemberPreferredPhone', 'MemberAddress1', 'MemberCity', 'MemberState', 'MemberPostalCode', 'MemberMlsId', 'OfficeName', 'OfficeKey', 'OfficeMlsId', 'MemberType'];

    public function agent_database(Request $request) {

        $states = LocationData::ActiveStates();

        return view('/marketing/data/agent_database', compact('states'));

    }

    public function get_results(Request $request) {

        $list_type = $request -> list_type;
        $states = $request -> states;
        $locations = $request -> counties;
        $office_codes = $request -> offices ?? null;
        $offices = null;
        $agent_count = '0';
        $file_location = null;

        if ($locations) {
            $counties = [];
            foreach ($locations as $location) {
                $parts = explode('-', $location);
                $counties[] = ['state' => $parts[0], 'county' => $parts[1]];
            }
            $counties = json_decode(json_encode($counties));

            if ($office_codes) {
                $offices = $this -> get_offices('', $list_type, '', $office_codes);
            } else {
                $offices = $this -> get_offices('', $list_type, $counties, null);
            }

            $agents = [];
            $file_name = 'agent_list_'.time().'.csv';
            $file = Storage::path('/tmp/'.$file_name);
            $handle = fopen($file, 'w');
            fputcsv($handle, $this -> agent_columns, ',');
            foreach ($offices as $office) {
                foreach ($office -> agents as $agent) {
                    $agents[] = $agent -> toArray();
                    fputcsv($handle, $agent -> toArray(), ',');
                }
            }
            $agent_count = count($agents);
            $file_location = '/storage/tmp/'.$file_name;

        }

        return view('/marketing/data/get_results_html', compact('agent_count', 'list_type', 'file_location'));



    }

    public function location_data(Request $request) {

        $states_data = $request -> states;
        $counties_data = $request -> counties ?? [];
        $counties = [];

        if ($states_data) {
            $counties = LocationData::select(['county', 'state'])
            -> whereIn('state', $states_data)
            -> where('county', '!=', '')
            -> groupBy('state')
            -> groupBy('county')
            -> orderBy('state')
            -> orderBy('county')
            -> get();

        }

        return compact('counties');

    }

    public function search_offices(Request $request) {

        $search_value = $request -> val;
        $list_type = $request -> list_type;
        $counties = json_decode($request -> counties);
        $offices = null;

        if ($search_value != '') {
            $offices = $this -> get_offices($search_value, $list_type, $counties, null);
        }

        return view('/marketing/data/office_search_results_html', compact('offices'));

    }

    public function get_offices($search_value, $list_type, $counties, $office_codes) {

        $offices = null;
        $state_and_county = [];
        $states = [];

        if ($office_codes) {

            $offices = BrightOffices::whereIn('OfficeMlsId', $office_codes)
            -> whereHas('agents', function (Builder $query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
            })
            -> with(['agents' => function ($query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
                $query -> select($this -> agent_columns);
            }])
            -> get();

        } else {

            foreach ($counties as $county) {
                $state = $county -> state;
                $states[] = $county -> state;
                $county = $county -> county;
                $state_and_county[] = ['state' => $state, 'county' => $county];
            }

            $states = array_unique($states);

            $locations = [];
            foreach ($states as $state) {
                $details['state'] = $state;
                $counties = [];
                foreach ($state_and_county as $data) {
                    if ($data['state'] == $state) {
                        if ($data['county'] != '') {
                            $counties[] = $data['county'];
                        }
                    }
                }
                $details['counties'] = $counties;
                $locations[] = $details;
            }

            $offices = BrightOffices::select(['OfficeKey', 'OfficeMlsId', 'OfficeName', 'OfficeAddress1', 'OfficeCity', 'OfficeStateOrProvince', 'OfficePostalCode'])
            -> where(function ($query) use ($search_value) {
                if ($search_value != '') {
                    $query -> where('OfficeName', 'like', '%'.$search_value.'%');
                }
            })
            -> where(function ($query) use ($locations) {
                foreach ($locations as $location) {
                    $query -> orWhere(function ($query) use ($location) {
                        $query -> where('OfficeStateOrProvince', $location['state'])
                        -> where(function ($query) use ($location) {
                            if ($location['state'] != 'DC') {
                                $query -> whereIn('OfficeCounty', $location['counties']);
                            }
                        });
                    });
                }
            })
            -> whereHas('agents', function (Builder $query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
            })
            -> with(['agents' => function ($query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
                $query -> select($this -> agent_columns);
            }])
            -> get();

        }

        return $offices;

    }

}
