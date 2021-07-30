<?php

namespace App\Http\Controllers\OldDB\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OldDB\Company\Documents;
use App\Models\OldDB\Company\Transactions;

class OldTransactionsController extends Controller
{

    public function get_transactions(Request $request) {

        $transactions = Transactions::select(['ListingSourceRecordId', 'ListingSourceRecordKey', 'FullStreetAddress'])
        -> with(['docs'])
        -> where('ListingSourceRecordId', 'AA8519306') -> get();

        foreach ($transactions as $transaction) {
            $docs = $transaction -> docs;
            if($docs) {
                dd($docs);
            }
        }

    }


}
