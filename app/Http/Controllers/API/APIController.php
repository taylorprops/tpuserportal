<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;

class APIController extends Controller {
    public function update_loan(Request $request) {

        return 'working';
        return Helper::parse_address_google('777 7th St NW #310 Washington, D.C., DC 20001');

        $loan_id = $request -> loan_id[0];
        $loan = Loans::find($loan_id);

        if ($loan) {
            return response() -> json(['found', 'yes']);
        }

        return response() -> json(['found', 'no']);

    }



}
