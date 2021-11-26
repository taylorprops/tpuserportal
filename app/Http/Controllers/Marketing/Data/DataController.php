<?php

namespace App\Http\Controllers\Marketing\Data;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BrightMLS\BrightOffices;
use App\Models\DocManagement\Resources\LocationData;

class DataController extends Controller
{
    public function agent_database(Request $request) {

        // $states = LocationData::select('state') -> groupBy('state') -> orderBy('state') -> get();
        $states = BrightOffices::select('OfficeStateOrProvince') -> groupBy('OfficeStateOrProvince') -> orderBy('OfficeStateOrProvince') -> get();

        return view('/marketing/data/agent_database', compact('states'));

    }

    public function location_data(Request $request) {

        $states_data = $request -> states;
        $counties_data = $request -> counties ?? [];

        $counties = LocationData::select(['county', 'state'])
        -> whereIn('state', $states_data)
        -> groupBy('state')
        -> groupBy('county')
        -> orderBy('state')
        -> orderBy('county')
        -> get();
        /* $cities = LocationData::select(['city', 'state'])
        -> whereIn('state', $states_data)
        -> where(function($query) use ($counties_data) {
            if (count($counties_data) > 0) {
                $query -> whereIn('county', $counties_data);
            }
        })
        -> groupBy('state')
        -> groupBy('city')
        -> orderBy('state')
        -> orderBy('city')
        -> get(); */

        return compact(/* 'cities',  */'counties');

    }

    public function search_offices(Request $request) {

        $val = $request -> val;
        $counties = $request -> counties;
        $offices = null;

        if ($val != '') {
            $state_and_county = [];
            $states = [];

            foreach ($counties as $county) {
                $county = json_decode($county);
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


            $offices = BrightOffices::where('OfficeName', 'like', '%'.$val.'%')
            -> where(function ($query) use ($locations) {
                foreach ($locations as $location) {
                    $query -> where('OfficeStateOrProvince', $location['state'])
                    -> whereIn('OfficeCounty', $location['counties']);
                }
            })
            -> has('agents')
            -> with(['agents'])
            -> get();
        }


        return view('/marketing/data/office_search_results_html', compact('offices'));

    }

}
