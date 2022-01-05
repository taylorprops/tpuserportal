<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use Illuminate\Http\Request;

class APIController extends Controller {
    public function update_loan(Request $request) {

        return $this -> parse_address_google('777 7th St NW #310 Washington, D.C., DC 20001');

        $loan_id = $request -> loan_id[0];
        $loan = Loans::find($loan_id);

        if ($loan) {
            return response() -> json(['found', 'yes']);
        }

        return response() -> json(['found', 'no']);

    }

    public function parse_address_google($address) {

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=' . urlencode($address).'&key='.config('global.google_api_key');
        $results = json_decode(file_get_contents($url), 1);

        $parts = array(
            'address' => array('street_number', 'route'),
            'unit' => array('subpremise'),
            'city' => array('locality'),
            'state' => array('administrative_area_level_1'),
            'zip' => array('postal_code'),
        );

        if (!empty($results['results'][0]['address_components'])) {
            $ac = $results['results'][0]['address_components'];

            foreach ($parts as $need => &$types) {

                foreach ($ac as &$a) {

                    if (in_array($a['types'][0], $types)) {
                        $address_out[$need] = $a['short_name'];
                    } elseif (empty($address_out[$need])) {
                        $address_out[$need] = '';
                    }

                }

            }

        } else {
            return 'error';
        }

        return $address_out;

    }

}
