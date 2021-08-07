<?php

namespace App\Http\Controllers\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocManagement\Archives\Documents;
use App\Models\DocManagement\Archives\Transactions as ArchivedTransactions;

class ArchivedTransactionsController extends Controller
{

    public function transactions_archived(Request $request) {

        $search_value = $request -> search_value;
        $transactions = ArchivedTransactions::select(['listingGuid', 'saleGuid', 'property', 'agentId', 'listingDate', 'actualClosingDate', 'status'])
        -> where('address', 'like', '%'.$search_value.'%')
        -> orWhere('agent_name', 'like', '%'.$search_value.'%')
        -> with(['agent_details:id,nickname,last'])
        -> orderBy('actualClosingDate', 'desc')
        -> paginate(25);

        return view('/doc_management/transactions/transactions_archived', compact('transactions'));

    }


    public function transactions_archived_view(Request $request) {

        $transaction = ArchivedTransactions::where('listingGuid', $request -> listingGuid)
        -> where('saleGuid', $request -> saleGuid)
        -> with(['agent_details:id,nickname,last,cell_phone,email1', 'docs'])
        -> first();

        $property = json_decode($transaction -> property);
        $address = $property -> streetNumber.' '.$property -> streetAddress.' '.$property -> city.', '.$property -> state.' '.$property -> zip;

        $agent = $transaction -> agent_details;
        $docs = $transaction -> docs;

        return view('/doc_management/transactions/transactions_archived_view', compact('transaction', 'address', 'agent', 'docs'));

    }

    public function add_missing_fields() {

        $transactions = ArchivedTransactions::whereNull('address')
        -> with(['agent_details'])
        -> limit(100)
        -> get();

        foreach($transactions as $transaction) {

            $property = json_decode($transaction -> property, true);
            $address = $property['streetNumber'];
            if($property['direction'] != '') {
                $address .= ' ' . $property['direction'];
            }
            $address .= ' ' .$property['streetAddress'];
            if($property['unit'] != '') {
                $address .= ' ' . $property['unit'];
            }
            $city = $property['city'];
            $state = $property['state'];
            $zip = $property['zip'];

            $agent_name = '';
            if($transaction -> agent_details) {
                $agent = $transaction -> agent_details;
                $agent_name = $agent -> nickname.' '.$agent -> last;
            }

            $transaction -> address = $address;
            $transaction -> city = $city;
            $transaction -> state = $state;
            $transaction -> zip = $zip;
            $transaction -> agent_name = $agent_name;
            $transaction -> save();

        }

    }

}
