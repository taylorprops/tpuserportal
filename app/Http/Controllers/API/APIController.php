<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use TheIconic\NameParser\Parser;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use App\Models\DocManagement\Resources\LocationData;

class APIController extends Controller {

    public function update_loan(Request $request) {

        $client_id = $request -> client_id;
        if($client_id != 'd7acee3e89454909ae18d06e9a18c077') {
            abort(403);
        }

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


        // Address
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

        // Borrowers
        $borrower_fullname = $request -> borrower;
        $borrower = $this -> parse_name($borrower_fullname);
        $borrower_first = $borrower['first'];
        $borrower_last = $borrower['last'];
        $co_borrower_fullname = $request -> co_borrower;
        $co_borrower = $this -> parse_name($co_borrower_fullname);
        $co_borrower_first = $co_borrower['first'];
        $co_borrower_last = $co_borrower['last'];

        return [
            'borrower_first' => $borrower_first,
            'borrower_last' => $borrower_last,
        ];
        $loan = Loans::find($lending_pad_id);

        if ($loan) {
            return response() -> json(['found', 'yes']);
        }

        return response() -> json(['found', 'no']);

    }

    public function parse_name($name) {

        $parser = new Parser();
        $name = $parser -> parse($name);
        $first =  $name -> getFirstname();
        $middle =  $name -> getMiddlename();
        $last =  $name -> getLastname();
        $suffix =  $name -> getSuffix();

        if($suffix) {
            $last .= ', '.$suffix;
        }

        return [
            'first' => $first,
            'middle' => $middle,
            'last' => $last,
            'suffix' => $suffix,
        ];

    }



}
