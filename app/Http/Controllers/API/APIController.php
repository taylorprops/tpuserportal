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

        // verify request is coming from our lending pad account
        $client_id = $request -> client_id;
        if($client_id != 'd7acee3e89454909ae18d06e9a18c077') {
            abort(403);
        }

        $lending_pad_id = $request -> loan_id;
        $lender = null;
        $loan_number = null;
        $street = null;
        $city = null;
        $state = null;
        $zip = null;
        $county = null;
        $borrower_first = null;
        $borrower_last = null;
        $borrower_fullname = null;
        $co_borrower_first = null;
        $co_borrower_last = null;
        $co_borrower_fullname = null;
        $loan_type = null;
        $loan_amount = null;
        $note_rate = null;
        $locked = null;
        $lock_date = null;
        $lock_expiration = null;

        $loan_officer = null;
        $processor = null;


        // Address
        $address = $request -> address ?? null;
        if($address) {
            $address = Helper::parse_address_google($address);
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
        }

        // Borrowers
        $borrower_fullname = $request -> borrower ?? null;
        if($borrower_fullname) {
            $borrower = $this -> parse_name($borrower_fullname);
            $borrower_first = $borrower['first'].' '.$borrower['middle'];
            $borrower_last = $borrower['last'];
        }
        $co_borrower_fullname = $request -> co_borrower ?? null;
        if($co_borrower_fullname) {
            $co_borrower = $this -> parse_name($co_borrower_fullname);
            $co_borrower_first = $co_borrower['first'].' '.$co_borrower['middle'];
            $co_borrower_last = $co_borrower['last'];
        }



        $loan = Loans::where(function($query) use ($lending_pad_id) {
            $query -> where('lending_pad_id', $lending_pad_id)
            -> whereNotNull('lending_pad_id');
        })
        -> orWhere(function($query) use ($loan_number) {
            $query -> where('loan_number', $loan_number)
            -> whereNotNull('loan_number');
        })
        -> first();

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
