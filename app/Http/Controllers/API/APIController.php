<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use App\Models\DocManagement\Resources\LocationData;

class APIController extends Controller {

    public function update_loan(Request $request) {

        return $_SERVER['HTTP_REFERER'];

        $lending_pad_id = $request -> loan_id;
        $street = '';
        $city = '';
        $state = '';
        $zip = '';
        $county = '';
        $borrower_first = '';
        $borrower_last = '';
        $borrower_fullname = '';
        $co_borrower_first = '';
        $co_borrower_last = '';
        $co_borrower_fullname = '';



        $address = Helper::parse_address_google($request -> address);
        $street_number = $address['street_number'] ?? null;
        $street_address = $address['address'] ?? null;
        $unit = $address['unit'] ?? null;
        $street = trim($street_number.' '.$street_address);
        if($unit != '') {
            $street += ' '.$unit;
        }
        $city = $address['city'] ?? null;
        $state = $address['state'] ?? null;
        $zip = $address['zip'] ?? null;
        if($zip) {
            $zip_lookup = LocationData::where('zip', $zip) -> first();
            $county = $zip_lookup -> county;
        }

        $loan = Loans::find($lending_pad_id);

        if ($loan) {
            return response() -> json(['found', 'yes']);
        }

        return response() -> json(['found', 'no']);

    }



}
