<?php
/* header("Access-Control-Allow-Origin: *"); */
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;

class APIController extends Controller
{
    public function update_loan(Request $request) {

        return 'working';
        $loan_id = $request -> loan_id[0];
        $loan = Loans::find($loan_id);

        if($loan) {
            return response() -> json(['found', 'yes']);
        }
        return response() -> json(['found', 'no']);

    }
}
