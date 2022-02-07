<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;

class APIController extends Controller {

    public function update_loan(Request $request) {

        $lending_pad_id = $request -> loan_id;
        $address = Helper::parse_address_google($request -> address);
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

        return $street.' '.$city.' '.$state.' '.$zip;

        $loan = Loans::find($lending_pad_id);

        if ($loan) {
            return response() -> json(['found', 'yes']);
        }

        return response() -> json(['found', 'no']);

    }



}
