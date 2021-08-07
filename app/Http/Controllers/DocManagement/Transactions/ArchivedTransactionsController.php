<?php

namespace App\Http\Controllers\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocManagement\Archives\Documents;
use App\Models\DocManagement\Archives\Transactions as ArchivedTransactions;

class ArchivedTransactionsController extends Controller
{

    public function transactions_archived(Request $request) {

        $search = $request -> search;
        $transactions = ArchivedTransactions::select(['listingGuid', 'saleGuid', 'property', 'agentId', 'listingDate', 'actualClosingDate', 'status'])
        -> where(function($query) use ($search) {
            if($search) {
                $query -> where('address', 'like', '%'.$search.'%')
                -> orWhere('agent_name', 'like', '%'.$search.'%');
            }
        })
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



}
