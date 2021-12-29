<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;

class APIController extends Controller
{
    public function check_loan_exists(Request $request) {

        $loan_id = $request -> loan_id;
        $loan = Loans::find($loan_id);

        if($loan) {
            return response() -> json(['found', 'yes']);
        }
        return response() -> json(['found', 'no']);

    }
}
