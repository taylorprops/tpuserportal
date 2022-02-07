<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;

class APIController extends Controller {

    public function update_loan(Request $request) {

        $data = json_decode($request -> data, true);
        return $data[0];

        $lending_pad_id = $request -> loan_id;
        $address = Helper::parse_address_google('777 7th St NW #310 Washington, D.C., DC 20001');
        $street_number = $address['street_number'];
        $street_address = $address['address'];
        $unit = $address['unit'];
        $street = $street_number.' '.$street_address;
        if($unit != '') {
            $street += ' '.$unit;
        }
        $city = $address['city'];
        $state = $address['state'];
        $zip = $address['zip'];

        $loan = Loans::find($lending_pad_id);

        if ($loan) {
            return response() -> json(['found', 'yes']);
        }

        return response() -> json(['found', 'no']);

    }



}
